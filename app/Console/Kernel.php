<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Exports\ActivityLogExport;
use App\Exports\SMSExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Spatie\Activitylog\Contracts\Activity; 
use Mail;

use App\Mail\Alert;
use App\Mail\DevReport;

use App\ActivityLog;
use App\Conversation;
use App\Count;
use App\Coupon;
use App\Messages;
use App\Order;
use App\OrderItem;
use App\Product;
use App\SMS;
use App\VendorSubscription;
use App\VSPayment;
use App\WTUPayment;



class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    protected function scheduleTimezone()
    {
        return 'Africa/Accra';
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*--- SMS and Activity Log Reports and Table Clears ---*/
        $schedule->call(function () {
            //generate exports
            Excel::store(new ActivityLogExport, '/reports/activity/activity-report-'.date('Y-m-d').'.csv');
            Excel::store(new SMSExport, '/reports/sms/sms-report-'.date('Y-m-d').'.csv');

            //empty tables
            SMS::truncate();
            ActivityLog::truncate();

            //send report
            $data = array(
                'subject' => "SMS and Activity Reports for ".date("F Y"),
                'name' => "Solushop Dev Team",
                'message' => "SMS and Activity Reports for ".date("F Y")." generated successfully. Please find attached."
            );

            Mail::to("dev@solushop.com.gh", "Dev")
                ->queue(new DevReport($data));

            /*--- log activity ---*/
            activity()
            ->tap(function(Activity $activity) {
                $activity->causer_type = 'App\System';
                $activity->causer_id = '-';
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Reports Exported and Cleared (Automated Task)';
            })
            ->log('Reports generated and exported to Development Team successfully (dev@solushop.com.gh)');

        })->monthlyOn(date('t'), "23:59");

        /*--- Process SMS Queue ---*/
        $schedule->call(function () {
            $sms_queue_items = SMS::
            whereIn('sms_state', ['1', '3'])
            ->get();

            foreach ($sms_queue_items as $sms_queue_item) {
                //send request
                $url = "http://api.smsgh.com/v3/messages/send?" . "From=Solushop-GH" . "&To=%2B" . urlencode($sms_queue_item->sms_phone) . "&Content=" . urlencode($sms_queue_item->sms_message) . "&ClientId=dylsfikt" . "&ClientSecret=rrllqthk" . "&RegisteredDelivery=true";
                $ch     = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_URL, $url);
                $response = curl_exec($ch);
                if ($response) {
                    $sms_queue_item->sms_state = 2;
                }else{
                    $sms_queue_item->sms_state = 3;
                }

                $sms_queue_item->save();
            }
        })->everyMinute();

        /*--- Vendor Subscriptions Check ---*/
        $schedule->call(function () {
            $vendor_subscriptions = VendorSubscription::
            where([
                ['vs_days_left', ">", 0]
            ])
            ->with('vendor')
            ->get();

            foreach ($vendor_subscriptions as $vendor_subscription) {
                //reduce the day by 1
                $vendor_subscription->vs_days_left--;

                //send message where necessary
                switch ($vendor_subscription->vs_days_left) {
                    case 5:
                        /*--- Notify Vendor ---*/
                        $sms_message = "Hiya ".$vendor_subscription->vendor->name.", time flies when you're with the right people. Looks like your subscription is about expiring. Kindly extend your subscription when you have the chance to. You have 5 days left.";

                        $sms = new SMS;
                        $sms->sms_message = $sms_message;
                        $sms->sms_phone = $vendor_subscription->vendor->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        /*--- Notify Vendor via Mail ---*/
                        $data = array(
                            'subject' => '5 days to Subscription Expiry - Solushop Ghana',
                            'name' => $vendor_subscription->vendor->name,
                            'message' => "Time flies when you're with the right people. Looks like your subscription is about expiring. Kindly extend your subscription when you have the chance to. You have 5 days left."
                        );

                        Mail::to($vendor_subscription->vendor->email, $vendor_subscription->vendor->name)
                            ->queue(new Alert($data));

                        break;

                    case 3:
                        /*--- Notify Vendor ---*/
                        $sms_message = "Heya ".$vendor_subscription->vendor->name.", did you forget to extend your subscription? No worries, we're here to remind you. You have 3 days left on your subscription.";

                        $sms = new SMS;
                        $sms->sms_message = $sms_message;
                        $sms->sms_phone = $vendor_subscription->vendor->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        /*--- Notify Vendor via Mail ---*/
                        $data = array(
                            'subject' => '3 days to Subscription Expiry - Solushop Ghana',
                            'name' => $vendor_subscription->vendor->name,
                            'message' => "Did you forget to extend your subscription? No worries, we're here to remind you. You have 3 days left on your subscription."
                        );

                        Mail::to($vendor_subscription->vendor->email, $vendor_subscription->vendor->name)
                            ->queue(new Alert($data));
                        
                        break;
                    
                    case 1:
                        /*--- Notify Vendor ---*/
                        $sms_message = "Hi ".$vendor_subscription->vendor->name.", please dont leave. You have only 24 hours remaining on your subscription. Please extend your subscription ASAP! We don't want to lose you.";

                        $sms = new SMS;
                        $sms->sms_message = $sms_message;
                        $sms->sms_phone = $vendor_subscription->vendor->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        $data = array(
                            'subject' => '1 day to Subscription Expiry - Solushop Ghana',
                            'name' => $vendor_subscription->vendor->name,
                            'message' => "Please dont leave. You have only 24 hours remaining on your subscription. Please extend your subscription as soon as possible. We don't want to lose you."
                        );

                        Mail::to($vendor_subscription->vendor->email, $vendor_subscription->vendor->name)
                            ->queue(new Alert($data));
                        
                        break;
                    
                    case 0:
                        //subscription ended - deactivate products
                        Product::where([
                            ['product_vid', '=', $vendor_subscription->vendor->id],
                            ['product_state', '=', '1']
                        ])->update([
                            'product_state' => '5'
                        ]);

                        /*--- Notify Vendor ---*/
                        $sms_message = "Hi ".$vendor_subscription->vendor->name.", we hate to see you go but your subscription has expired. Please reactivate under the subscription tab in your portal. Please don't keep us missing you for too long.";

                        $sms = new SMS;
                        $sms->sms_message = $sms_message;
                        $sms->sms_phone = $vendor_subscription->vendor->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        $data = array(
                            'subject' => 'Subscription Expired - Solushop Ghana',
                            'name' => $vendor_subscription->vendor->name,
                            'message' => "We hate to see you go but your subscription has expired. Please reactivate under the subscription tab in your portal. Please don't keep us missing you for too long."
                        );

                        Mail::to($vendor_subscription->vendor->email, $vendor_subscription->vendor->name)
                            ->queue(new Alert($data));
                        
                        break;
                    default:
                        # code...
                        break;
                }

                $vendor_subscription->save();
            }
        
        })->dailyAt('10:00');

        /*--- Count Update ---*/
        $schedule->call(function () {
            $count = Count::first();

            $count->customer_count = 0;
            $count->product_count = 0;
            $count->vendor_count = 0;
            $count->order_count = 0;

            $count->save();
        
        })->dailyAt('23:59');

         /*--- Delete empty conversations older than 3 days ---*/
         $schedule->call(function () {
            $three_days_ago = date('Y-m-d H:i:s', strtotime('-3 days', strtotime(date('Y-m-d'))));

            $conversations = Conversation::
                where([
                    ['created_at', '<', $three_days_ago]
                ])
                ->with('messages')
                ->get()
                ->toArray();

            

            for ($i=0; $i < sizeof($conversations); $i++) { 
                if(sizeof($conversations[$i]["messages"]) < 1){
                    //delete
                    Conversation::where([
                        ['id', '=', $conversations[$i]["id"]]
                    ])->delete();
                }
            }
        
        })->dailyAt('23:59');

         /*--- Delete unpaid orders older than 7 days ---*/
         $schedule->call(function () {
            $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days', strtotime(date('Y-m-d'))));

            $orders = Order::
                where([
                    ['order_date', '<', $seven_days_ago],
                    ['order_state', '=', 1]
                ])
                ->get()
                ->toArray();

            

            for ($i=0; $i < sizeof($orders); $i++) { 
                //delete order items
                OrderItem::where([
                    ['oi_order_id', '=', $orders[$i]["id"]]
                ])->delete();

                //delete order
                Order::where([
                    ['id', '=', $orders[$i]["id"]]
                ])->delete();
            }
        })->dailyAt('2:00');

        /*--- Delete unpaid wallet top up payments ---*/
        $schedule->call(function () {

            WTUPayment::where([
                ['wtu_payment_status', '=', "UNPAID"]
            ])->delete();
            
        })->dailyAt('2:00');

        /*--- Delete unpaid subscription payments ---*/
        $schedule->call(function () {

            VSPayment::where([
                ['vs_payment_State', '=', "UNPAID"]
            ])->delete();
            
        })->dailyAt('2:00');

        /*--- Update expired coupons ---*/
        $schedule->call(function () {

            Coupon::where([
                ['coupon_owner', '=', "SOLUSHOP"],
                ['coupon_state', '=', '2'],
                ['coupon_expiry_date', '<', date("Y-m-d")]
            ])->update([
                'coupon_state' => '4'
            ]);
            
        })->dailyAt('23:59');

        /*--- Check for pending approvals ---*/
        $schedule->call(function () {
            //select count of pending approvals
            $count = Product::
            where('product_state', '2')
            ->get()->count();

            if ($count > 0) {
                if ($count == 1) {
                    $message = "There is 1 product pending approval. Kindly review and approve or reject.";
                }else{
                    $message = "There are $count products pending approval. Kindly review and approve or reject.";
                }

                //send alert
                $data = array(
                    'subject' => "Products Pending Approval",
                    'name' => "Support",
                    'message' => $message
                );

                Mail::to("support@solushop.com.gh", "Support")
                    ->queue(new Alert($data));
            }

            

        })->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
