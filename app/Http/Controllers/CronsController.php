<?php

namespace App\Http\Controllers;

use App\Exports\ActivityLogExport;
use App\Exports\SMSExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

use App\ActivityLog;
use App\Count;
use App\Product;
use App\SMS;
use App\VendorSubscription;

class CronsController extends Controller
{
    public function generateReports(){
        //generate exports
        Excel::store(new ActivityLogExport, '/reports/activity/'.date('Y-m-d').'.csv');
        Excel::store(new SMSExport, '/reports/sms/'.date('Y-m-d').'.csv');

        //empty tables
        SMS::truncate();
        ActivityLog::truncate();
    }


    public function processSMSQueue(){
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
    }

    public function updateCounts(){
        $count = Count::first();

        $count->customer_count = 0;
        $count->product_count = 0;
        $count->vendor_count = 0;
        $count->order_count = 0;

        $count->save();
        
    }

    public function checkVendorSubscriptions(){
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
                    //notify vendor
                    $sms_message = "Hiya ".$vendor_subscription->vendor->name.", time flies when you're with the right people. Looks like your subscription is about expiring. Kindly extend your subscription when you have the chance to. You have 5 days left.";

                    $sms = new SMS;
                    $sms->sms_message = $sms_message;
                    $sms->sms_phone = $vendor_subscription->vendor->phone;
                    $sms->sms_state = 1;
                    $sms->save();

                    break;

                case 3:
                    //notify vendor
                    $sms_message = "Heya ".$vendor_subscription->vendor->name.", did you forget to extend your subscription? No worries, we're here to remind you. You have 3 days left on your subscription.";

                    $sms = new SMS;
                    $sms->sms_message = $sms_message;
                    $sms->sms_phone = $vendor_subscription->vendor->phone;
                    $sms->sms_state = 1;
                    $sms->save();
                    
                    break;
                
                case 1:
                    //notify vendor
                    $sms_message = "Hi ".$vendor_subscription->vendor->name.", please dont leave. You have only 24 hours remaining on your subscription. Please extend your subscription ASAP! We don't want to lose you.";

                    $sms = new SMS;
                    $sms->sms_message = $sms_message;
                    $sms->sms_phone = $vendor_subscription->vendor->phone;
                    $sms->sms_state = 1;
                    $sms->save();
                    
                    break;
                   
                case 0:
                    //subscription ended - deactivate products
                    Product::where([
                        ['product_vid', '=', $vendor_subscription->vendor->id],
                        ['product_state', '=', '1']
                    ])->update([
                        'product_state' => '5'
                    ]);

                    //notify vendor
                    $sms_message = "Hi ".$vendor_subscription->vendor->name.", we hate to see you go but your subscription has expired. Please reactivate under the subscription tab in your portal. Please don't keep us missing you for too long.";

                    $sms = new SMS;
                    $sms->sms_message = $sms_message;
                    $sms->sms_phone = $vendor_subscription->vendor->phone;
                    $sms->sms_state = 1;
                    $sms->save();
                    
                    break;
                default:
                    # code...
                    break;
            }

            $vendor_subscription->save();
        }
    }
}
