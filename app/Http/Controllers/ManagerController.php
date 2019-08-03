<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Validator;
use Auth;

use App\ActivityLog;
use App\Count;
use App\Manager;
use App\Order;
use App\Product;
use App\SABadge;
use App\SalesAssociate;
use App\SMS;
use App\VendorSubscription;



class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:manager');
    }

    public function showCustomers(){

    }

    public function showCustomer(){

    }

    public function processCustomer(){

    }

    public function showOrders(){

    }

    public function processOrders(){

    }

    public function showOrder(){

    }

    public function processOrder(){

    }

    public function showMessages(){

    }

    public function showFlaggedMessages(){

    }

    public function showDeletedMessages(){

    }

    public function showConversation(){

    }

    public function processConversation(){

    }

    public function showProducts(){

    }

    public function showDeletedProducts(){

    }

    public function showAddProduct(){

    }

    public function processAddProduct(){

    }

    public function showProduct(){

    }

    public function processProduct(){

    }

    public function showVendors(){

    }

    public function showAddVendor(){

    }

    public function processAddVendor(){

    }

    public function showVendor(){

    }

    public function processVendor(){

    }

    public function showPickupHistory(){

    }

    public function showActivePickups(){

    }

    public function processActivePickups(){

    }

    public function showDeliveryHistory(){

    }

    public function showActiveDeliveries(){

    }

    public function processActiveDeliveries(){

    }

    public function showCoupons(){

    }

    public function showGenerateCoupon(){

    }

    public function processGenerateCoupon(){
        
    }

    public function showSalesAssociates(){
        return view('portal.main.manager.sales-associates')
                ->with('sales_associates',  SalesAssociate::all()->toArray());
    }

    public function showAddSalesAssociate(){
        return view('portal.main.manager.add-sales-associate');
    }

    public function processAddSalesAssociate(Request $request){
         //validate form data
         $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'mode_of_payment' => 'required',
            'payment_details' => 'required',
            'residential_address' => 'required', 
            'type_of_identification' => 'required',
            'identification_file' => 'required'
        ]);

        if ($validator->fails()) {
            $messageType = "error_message";
            $messageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $messageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput(['first_name', 'last_name', 'email', 'phone', 'mode_of_payment', 'payment_details', 'residential_address', 'type_of_identification', 'identification_file'])->with($messageType, $messageContent);
        }

        //check for email existence in system
        if (SalesAssociate::where('email', $request->email)->first()) {
            return redirect()->back()->withInput(['first_name', 'last_name', 'email', 'phone', 'mode_of_payment', 'payment_details', 'residential_address', 'type_of_identification', 'identification_file'])->with("error_message", "Email already associated with a Sales Associate");
        }
        //check for phone existence in system
        if (SalesAssociate::where('phone', "233".substr($request->phone, 1))->first()) {
            return redirect()->back()->withInput(['first_name', 'last_name', 'email', 'phone', 'mode_of_payment', 'payment_details', 'residential_address', 'type_of_identification', 'identification_file'])->with("error_message", "Phone already associated with a Sales Associate");
        }

        /*--- generate coupon ---*/
        //Random numeric character permitted characters
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        //part one
        $coupon_id   = 'S'.
        substr(str_shuffle($permitted_chars), 7, 2).
        date('d').
        "-".
        substr(str_shuffle($permitted_chars), 7, 2).
        date('m').
        'S'.
        "-".
        substr(str_shuffle($permitted_chars), 7, 1).
        substr(date('Y'), 0, 2).
        substr(str_shuffle($permitted_chars), 7, 2).
        "-".
        substr(str_shuffle($permitted_chars), 7, 1).
        substr(date('Y'), 2, 2);

        //part two
        $count = Count::first();
        $coupon_id .= substr("000".$count->coupon_count, strlen(strval($count->coupon_count)));

        /*--- save id file ---*/
        $identification_file = $request->file('identification_file');
        $identification_file_ext = $identification_file->getClientOriginalExtension();
        $sub_path = 'portal/s-team-member-id/'; 
        $destination_path = public_path($sub_path);  
        $identification_file->move($destination_path,  $coupon_id.".".$identification_file_ext);

        /*--- store associate data ---*/
        $sales_associate = new SalesAssociate;
        $sales_associate->first_name        = ucwords(strtolower($request->first_name));
        $sales_associate->last_name         = ucwords(strtolower($request->last_name));
        $sales_associate->phone             = "233".substr($request->phone, 1);
        $sales_associate->email             = $request->email;
        $sales_associate->passcode          = $passcode = rand(100000, 999999);
        $sales_associate->password          = bcrypt($passcode);
        $sales_associate->address           = ucwords($request->residential_address);
        $sales_associate->badge             = 1;
        $sales_associate->id_type           = $request->type_of_identification;
        $sales_associate->id_file           = $coupon_id.".".$identification_file_ext;
        $sales_associate->mode_of_payment   = $request->mode_of_payment;
        $sales_associate->payment_details   = ucwords($request->payment_details);
        $sales_associate->balance           = 0;
        $sales_associate->save();

        /*--- update counts ---*/
        $count->coupon_count++;
        $count->save();
        
        /*--- notify associate ---*/
        $sms = new SMS;
        $sms->sms_message = "Hi ".ucwords(strtolower($request->first_name)).", you have been accepted as a Sales Associate on Solushop.\n\nEmail: ".$request->email."\nPassword : $passcode\nLogin here : https://www.solushop.com.gh/portal/sales-associate";
        $sms->sms_phone = "233".substr($request->phone, 1);
        $sms->sms_state = 1;
        $sms->save();


         //Log activity
         activity()
         ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
         ->tap(function(Activity $activity) {
             $activity->subject_type = 'System';
             $activity->subject_id = '0';
             $activity->log_name = 'Sales Associate Registration';
         })
         ->log(Auth::guard('manager')->user()->email." added ".ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name))." as a sales associate");
         
         return redirect()->back()->with("success_message", ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name))." added successfully as a sales associate.");

        
    }

    public function showSalesAssociate($memberID){

        if (is_null(SalesAssociate::where('id', $memberID)->first())) {
            return redirect()->route("manager.show.sales.associates")->with("error_message", "Sales associate not found.");
        }
        $sales_associate = SalesAssociate::where('id', $memberID)->with('badge')->first()->toArray();


        $sales_associate["sales"] = Order::
            whereIn('order_state', [3, 4, 5, 6])
            ->where('order_scoupon', substr($sales_associate["id_file"], 0, 24))
            ->sum('order_subtotal');

        return view('portal.main.manager.view-sales-associate')
                ->with('sales_associate', $sales_associate);
    }

    public function processSalesAssociate(){
        
    }

    public function showAccounts(){
        
    }

    public function processAccounts(){
        
    }

    public function processSubscriptions(Request $request)
    {
        //select details
        $subscription = DB::select(
            "SELECT *, vendor_subscriptions.id as subscription_id, vendor_subscriptions.created_at as subscription_created_at, vendor_subscriptions.updated_at as subscription_updated_at FROM vendors, vendor_subscriptions, vs_packages WHERE vendors.id = vendor_subscriptions.vs_vendor_id AND vendor_subscriptions.vs_vsp_id = vs_packages.id AND vendor_subscriptions.id = :vendor_subscription_id",
            ['vendor_subscription_id' => $request->subscription_id]
        );

        if (is_null($subscription)) {
            return redirect()->back()->with("error_message", "Sorry, I can't seem to find that subscription");
        }

        //delete all non live products of that vendor
        Product::
            where([
                ['product_state', "<>", 1],
                ['product_vid', "=", $subscription[0]->vs_vendor_id]
            ])
            ->update([
                'product_state' => 4
            ]);

        //deactivate live products of that vendor
        Product::
            where([
                ['product_state', "=", 1],
                ['product_vid', "=", $subscription[0]->vs_vendor_id]
            ])
            ->update([
                'product_state' => 5
            ]);

        //update days left to 0
        VendorSubscription::
            where([
                ['vs_vendor_id', "=", $subscription[0]->vs_vendor_id]
            ])
            ->update([
                'vs_days_left' => 0
            ]);

        //notify vendor
        $sms = new SMS;
        $sms->sms_message = "Dear ".$subscription[0]->name.", your subscription as a vendor with Solushop Ghana has been cancelled.";
        $sms->sms_phone = $subscription[0]->phone;
        $sms->sms_state = 1;
        $sms->save();

        //Log activity
        activity()
        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Vendor Subscription Cancellation';
        })
        ->log(Auth::guard('manager')->user()->email." cancelled ".$subscription[0]->name."'s subscription.");
        
        return redirect()->back()->with("success_message", $subscription[0]->name."'s subscription has been successfully cancelled.");
    }

    public function showSubscriptions()
    {
        return view('portal.main.manager.subscriptions')
                ->with('subscriptions', DB::select(
                    "SELECT *, vendor_subscriptions.id as subscription_id, vendor_subscriptions.created_at as subscription_created_at, vendor_subscriptions.updated_at as subscription_updated_at FROM vendors, vendor_subscriptions, vs_packages WHERE vendors.id = vendor_subscriptions.vs_vendor_id AND vendor_subscriptions.vs_vsp_id = vs_packages.id"
                ));
    }

    public function showActivityLog()
    {
        return view('portal.main.manager.activity-log')
                ->with('activity', ActivityLog::all()->toArray());
    }

    public function showSMSReport()
    {
        return view('portal.main.manager.sms-report')
                ->with('sms', SMS::with('state')->get()->toArray());
    }

    public function index()
    {
        //get counts
        $dashboard['new_orders_count'] = count(Order::
                where('order_state', 2)
                ->get()
            );

        $dashboard['ongoing_orders_count'] = count(Order::
                whereIn('order_state', [3, 4, 5])
                ->get()
            );
        
        $dashboard['completed_orders_count'] = count(Order::
                where('order_state', 6)
                ->get()
            );

        $dashboard['cancelled_orders_count'] = count(Order::
            where('order_state', 7)
            ->get()
        );

        $dashboard['total_orders_count'] = count(Order::
            whereIn('order_state', [2, 3, 4, 5, 6, 7])
            ->get()
        );

        if($dashboard['new_orders_count'] > 0){
            $dashboard['new_orders'] = Order::
                where('order_state', 2)
                ->orderBy("order_date")
                ->with('order_items.sku.product.images', 'customer')
                ->get()
                ->toArray();
        }

        return view('portal.main.manager.dashboard')
            ->with('dashboard', $dashboard);
    }
}
