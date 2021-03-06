<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Auth;
use PDF;
use Image;
use Mail;

use App\Mail\Alert;

use App\AccountTransaction;
use App\ActivityLog;
use App\CartItem;
use App\Conversation;
use App\Count;
use App\Coupon;
use App\Customer;
use App\DeletedMessage;
use App\DeliveredItem;
use App\DeliveryPartner;
use App\Manager;
use App\Message;
use App\MessageFlag;
use App\Order;
use App\OrderItem;
use App\PickedUpItem;
use App\Product;
use App\ProductImage;
use App\ProductCategory;
use App\SABadge;
use App\SalesAssociate;
use App\SMS;
use App\StockKeepingUnit;
use App\Vendor;
use App\VendorSubscription;
use App\WishlistItem;



class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:manager');
    }

    public function showCustomers(){
        return view('portal.main.manager.customers')
                ->with('customers',  Customer::with('milk', 'chocolate')->get()->toArray());
    }

    public function showCustomer($customerID){
        if (is_null(Customer::where('id', $customerID)->with('milk', 'chocolate')->first())) {
            return redirect()->route("manager.show.customers")->with("error_message", "Customer not found");
        }

        return view('portal.main.manager.view-customer')
                ->with('customer',  Customer::where('id', $customerID)->with('milk', 'chocolate', 'addresses', 'orders.order_items.sku.product.images', 'orders.order_state')->first()->toArray());
    }

    public function processCustomer(Request $request, $customerID){
        /*--- validate ---*/
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits:10'
        ]);

        if ($validator->fails()) {
            $messageType = "error_message";
            $messageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $messageContent .= $messages[0]." "; 
            }

            return redirect()->back()->with($messageType, $messageContent);
        }

        $customer = Customer::where('id', $customerID)->first();
        $customer->first_name = ucwords(strtolower($request->first_name));
        $customer->last_name = ucwords(strtolower($request->last_name));
        $customer->email = strtolower($request->email);
        $customer->phone = "233".substr($request->phone, 1);
        $customer->save();


        /*--- log activity ---*/
        activity()
        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Customer Details Update';
        })
        ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." updated the details of customer, ".$request->first_name." ".$request->last_name);

        return redirect()->back()->with("success_message", ucwords(strtolower($request->first_name))."'s details updated successfully.");
    }

    public function showOrders(){
        $orders['new_orders_count'] = count(Order::
                where('order_state', 2)
                ->get()
            );

        $orders['ongoing_orders_count'] = count(Order::
                whereIn('order_state', [3, 4, 5])
                ->get()
            );
        
        $orders['completed_orders_count'] = count(Order::
                where('order_state', 6)
                ->get()
            );

        $orders['cancelled_orders_count'] = count(Order::
            where('order_state', 7)
            ->get()
        );

        $orders['total_orders_count'] = count(Order::
            whereIn('order_state', [2, 3, 4, 5, 6, 7])
            ->get()
        );

        $orders['all_orders'] = Order::
            orderBy("order_date")
            ->with('order_items.sku.product.images', 'customer', 'order_state')
            ->get()
            ->toArray();

        return view('portal.main.manager.orders')
            ->with('orders', $orders);
    }

    public function processOrders(Request $request){

        $orders["filter"] = $request->orders_filter;
        switch ($request->orders_filter) {
            case 'New':
                $orders['all_orders'] = Order::
                    orderBy("order_date")
                    ->where('order_state', 2)
                    ->with('order_items.sku.product.images', 'customer', 'order_state')
                    ->get()
                    ->toArray();
                break;
            case 'Ongoing':
                $orders['all_orders'] = Order::
                    orderBy("order_date")
                    ->whereIn('order_state', [3, 4, 5])
                    ->with('order_items.sku.product.images', 'customer', 'order_state')
                    ->get()
                    ->toArray();
                break;
            case 'Completed':
                $orders['all_orders'] = Order::
                    orderBy("order_date")
                    ->where('order_state', 6)
                    ->with('order_items.sku.product.images', 'customer', 'order_state')
                    ->get()
                    ->toArray();
                break;
            case 'Cancelled':
                $orders['all_orders'] = Order::
                    orderBy("order_date")
                    ->where('order_state', 7)
                    ->with('order_items.sku.product.images', 'customer', 'order_state')
                    ->get()
                    ->toArray();
                break;
            
            default:
                $orders["filter"] = null;
                $orders['all_orders'] = Order::
                    orderBy("order_date")
                    ->with('order_items.sku.product.images', 'customer', 'order_state')
                    ->get()
                    ->toArray();
                break;
        }

        $orders['new_orders_count'] = count(Order::
                where('order_state', 2)
                ->get()
            );

        $orders['ongoing_orders_count'] = count(Order::
                whereIn('order_state', [3, 4, 5])
                ->get()
            );
        
        $orders['completed_orders_count'] = count(Order::
                where('order_state', 6)
                ->get()
            );

        $orders['cancelled_orders_count'] = count(Order::
            where('order_state', 7)
            ->get()
        );

        $orders['total_orders_count'] = count(Order::
            whereIn('order_state', [2, 3, 4, 5, 6, 7])
            ->get()
        );


        return view('portal.main.manager.orders')
            ->with('orders', $orders);


    }

    public function showOrder($orderID){
        if (is_null(Order::
            where("id", $orderID)
            ->with('order_items.sku.product.images', 'customer', 'order_state')
            ->first()
            ->toArray())) {
            
            return redirect()->route("manager.show.orders")->with("error_message", "Order $orderID not found");
        }

        $order =  Order::
            where("id", $orderID)
            ->with('order_items.sku.product.images','order_items.order_item_state', 'customer', 'order_state', 'address', 'coupon.sales_associate.badge_info')
            ->first()
            ->toArray();

        $order["delivery_partner"] = DeliveryPartner::get()->toArray();

        if(strtotime($order["order_state"]["id"] == 6 AND $order["updated_at"]) < strtotime('-14 days') AND $order["dp_shipping"] == NULL){
            $order["allow_shipping_entry"] = "Yes";
        }

        /*--- Nets ---*/
        if ($order["order_state"]["id"] == 6) {
            //calculate profit or loss
            $order["profit_or_loss"] = 0;
            $order["profit_or_loss_item"] = [];

            //profit from order items
            for ($i=0; $i < sizeof($order["order_items"]); $i++) { 
                $order["profit_or_loss_item"]["description"][$i] = "Profit from ".$order["order_items"][$i]["oi_quantity"]." ".$order["order_items"][$i]["oi_name"];
                $order["profit_or_loss_item"]["amount"][$i] = $order["order_items"][$i]["oi_quantity"] * ( $order["order_items"][$i]["oi_selling_price"] - $order["order_items"][$i]["oi_settlement_price"]);
                $order["profit_or_loss"] += $order["profit_or_loss_item"]["amount"][$i];
            }

            //Shipping charged from customer
            $order["profit_or_loss_item"]["description"][$i] = "Shipping fee from ".$order["customer"]["first_name"]." ".$order["customer"]["last_name"];
            $order["profit_or_loss_item"]["amount"][$i] = $order["order_shipping"];
            $order["profit_or_loss"] += $order["profit_or_loss_item"]["amount"][$i];
            $i++;

            if($order["dp_shipping"] != NULL){
                //shipping paid to delivery partner
                $order["profit_or_loss_item"]["description"][$i] = "Shipping fee paid to Delivery Partner ";
                $order["profit_or_loss_item"]["amount"][$i] = -1 * $order["dp_shipping"];
                $order["profit_or_loss"] += $order["profit_or_loss_item"]["amount"][$i];
                $i++;
            }
            

            //loss from sales coupon if set
            if(isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL"){
                //1% discount on sale
                $order["profit_or_loss_item"]["description"][$i] = "1% discount from Sales Coupon ".$order["coupon"]["coupon_code"];
                $order["profit_or_loss_item"]["amount"][$i] = round(-0.01 * $order["order_subtotal"], 2);
                $order["profit_or_loss"] += $order["profit_or_loss_item"]["amount"][$i];
                $i++;

                //Commission to Sales Associate
                $order["profit_or_loss_item"]["description"][$i] = (100*$order["coupon"]["sales_associate"]["badge_info"]["sab_commission"])."% commission to Sales Associate  ".$order["coupon"]["sales_associate"]["first_name"]." ".$order["coupon"]["sales_associate"]["last_name"]." ( May have changed if SA was promoted )";
                $order["profit_or_loss_item"]["amount"][$i] = round(-1 * $order["coupon"]["sales_associate"]["badge_info"]["sab_commission"] * $order["order_subtotal"], 2);
                $order["profit_or_loss"] += $order["profit_or_loss_item"]["amount"][$i];
                $i++;
            }
        }

        return view('portal.main.manager.view-order')
                    ->with('order',$order);
    }

    public function processOrder(Request $request, $orderID){
        switch ($request->order_action) {
            case 'confirm_order_payment':
                $order = Order::
                    where("id", $orderID)
                    ->with('order_items.sku.product.images', 'order_items.sku.product.vendor', 'order_items.order_item_state', 'customer', 'order_state', 'address', 'coupon.sales_associate.badge_info')
                    ->first()
                    ->toArray();

                /*--- Update Account Balance | Record Transaction ---*/
                $count = Count::first();
                if(isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL"){
                    $count->account += round(( 0.99 * $order["order_subtotal"] ) + $order["order_shipping"], 2);
                }else{
                    $count->account += round($order["order_subtotal"] + $order["order_shipping"], 2);
                }
                $count->save();

                $transaction = new AccountTransaction;
                $transaction->trans_type = "Order Payment";
                if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL") {
                    $transaction->trans_amount = round(0.99 * $order["order_subtotal"], 2) + $order["order_shipping"];
                } else {
                    $transaction->trans_amount = $order["order_subtotal"] + $order["order_shipping"];
                }
                $transaction->trans_credit_account_type = 6;
                $transaction->trans_credit_account      = $order["order_customer_id"];
                $transaction->trans_debit_account_type  = 1;
                $transaction->trans_debit_account       = "INT-SC001";
                if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL") {
                    $transaction->trans_description         = $log = "Order Payment of GH¢ ".(round((0.99 * $order["order_subtotal"]), 2) + $order["order_shipping"])." from ".$order["customer"]["first_name"]." ".$order["customer"]["last_name"]." for order $orderID";
                } else {
                    $transaction->trans_description         = $log = "Order Payment of GH¢ ".($order["order_subtotal"] + $order["order_shipping"])." from ".$order["customer"]["first_name"]." ".$order["customer"]["last_name"]." for order $orderID";
                }
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $transaction->save();
                
                /*--- Update Order and Order Items | Reduce vendor stock | notify vendors ---*/
                //update order items sku
                for ($i=0; $i < sizeof($order["order_items"]); $i++) { 
                    $order_item_sku = StockKeepingUnit::where('id', $order["order_items"][$i]["sku"]["id"])->first();
                    $order_item_sku->sku_stock_left -= $order["order_items"][$i]["oi_quantity"];

                    //notify vendor
                    $sms = new SMS;
                    $sms->sms_message = "Purchase Alert\nItem: ".$order["order_items"][$i]["oi_name"]."\nQuantity Purchased: ".$order["order_items"][$i]["oi_quantity"]."\n Quantity Remaining: ".$order_item_sku->sku_stock_left;
                    $sms->sms_phone = $order["order_items"][$i]["sku"]["product"]["vendor"]["phone"];
                    $sms->sms_state = 1;
                    $sms->save();

                    $data = array(
                        'subject' => 'Purchase Alert - Solushop Ghana',
                        'name' => $order["order_items"][$i]["sku"]["product"]["vendor"]["name"],
                        'message' => "You have a new order.<br><br>Product: ".$order["order_items"][$i]["oi_name"]."<br>Quantity Purchased: ".$order["order_items"][$i]["oi_quantity"]."<br>Quantity Remaining: ".$order_item_sku->sku_stock_left."<br><br>"
                    );
        
                    Mail::to($order["order_items"][$i]["sku"]["product"]["vendor"]["email"], $order["order_items"][$i]["sku"]["product"]["vendor"]["name"])
                        ->queue(new Alert($data));

                    $order_item_sku->save();

                    $order_item = OrderItem::where('oi_sku', $order["order_items"][$i]["sku"]["id"])->first();
                    $order_item->oi_state = 2;
                    $order_item->save();
                }

                

                /*--- Accrue to Sales Associate if Coupon was used | Record Transaction | Promote Sales associate where necessary ---*/
                if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL") {
                    //sales associate old total sales
                    $ots = Order::
                        where("order_scoupon", $order["order_scoupon"])
                        ->whereIn("order_state", [3, 4, 5, 6])
                        ->sum('order_subtotal');

                    //sales associate new total sales
                    $nts = $ots + $order["order_subtotal"];

                    $sales_associate = SalesAssociate::
                        where('id', $order["coupon"]["sales_associate"]["id"])
                        ->first();

                    //update sales associate balance
                    $sales_associate->balance += round($order["coupon"]["sales_associate"]["badge_info"]["sab_commission"] * $order["order_subtotal"], 2);

                    //record transaction
                    $transaction = new AccountTransaction;
                    $transaction->trans_type                = "Sales Associate Accrual";
                    $transaction->trans_amount              = round($order["coupon"]["sales_associate"]["badge_info"]["sab_commission"] * $order["order_subtotal"], 2);
                    $transaction->trans_credit_account_type = 1;
                    $transaction->trans_credit_account      = "INT-SC001";
                    $transaction->trans_debit_account_type  = 7;
                    $transaction->trans_debit_account       = $order["coupon"]["sales_associate"]["id"];
                    $transaction->trans_description         = $log = "Accrual of GH¢ ".round($order["coupon"]["sales_associate"]["badge_info"]["sab_commission"] * $order["order_subtotal"], 2)." to ".$sales_associate->first_name." ".$sales_associate->last_name." for order $orderID";
                    $transaction->trans_date                = date("Y-m-d G:i:s");
                    $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                    $transaction->save();

                    //notify sales associate of update
                    $sms = new SMS;
                    $sms->sms_message = "Hi ".$sales_associate->first_name.", order $orderID made with your coupon has been confirmed. Your new balance is GHS ".$sales_associate->balance;
                    $sms->sms_phone = $sales_associate->phone;
                    $sms->sms_state = 1;
                    $sms->save();

                    $data = array(
                        'subject' => 'S.A. Order Confirmed  - Solushop Ghana',
                        'name' => $sales_associate->first_name,
                        'message' => "Order $orderID made with your coupon has been confirmed. Your new balance is GHS ".$sales_associate->balance
                    );
        
                    Mail::to($sales_associate->email, $sales_associate->first_name)
                        ->queue(new Alert($data));


                    //notify management
                    $managers = Manager::where('sms', 0)->get();
                    foreach ($managers as $manager) {

                        $data = array(
                            'subject' => 'New Order - Solushop Ghana',
                            'name' => $manager->first_name,
                            'message' => "This email is to inform you that a new order $orderID has been received. If you are not required to take any action during order processing, please treat this email as purely informational.<br><br>Customer: ".$order["customer"]["first_name"]." ".$order["customer"]["last_name"]."<br>Phone: 0".substr($order["customer"]["phone"], 3)
                        );

                        Mail::to($manager->email, $manager->first_name)
                            ->queue(new Alert($data));
                    }

                    //promote where necessary
                    if ($ots < 20000 && $nts >= 20000) {
                        //promote to elite
                        $sales_associate->badge = 4;

                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name.", you are now an Elite Sales Associate. You can now enjoy 4% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        $data = array(
                            'subject' => 'Promotion to Elite! - Solushop Ghana',
                            'name' => $sales_associate->first_name,
                            'message' => "Congrats! You are now an Elite Sales Associate. You can now enjoy 4% commission on all orders."
                        );
            
                        Mail::to($sales_associate->email, $sales_associate->first_name)
                            ->queue(new Alert($data));

                    }elseif($ots < 5000 && $nts >= 5000){
                        //promote to veteran
                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name.", you are now an Veteran Sales Associate. You can now enjoy 3% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();
                        $sales_associate->badge = 3;

                        $data = array(
                            'subject' => 'Promotion to Veteran! - Solushop Ghana',
                            'name' => $sales_associate->first_name,
                            'message' => "Congrats! You are now an Veteran Sales Associate. You can now enjoy 3% commission on all orders."
                        );
            
                        Mail::to($sales_associate->email, $sales_associate->first_name)
                            ->queue(new Alert($data));

                    }elseif($ots == 0 && $nts > 0){
                        //promote to rookie
                        $sales_associate->badge = 2;
                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name." on your first sale. You are now a Rookie sales associate. Keep selling to become a Veteran and enjoy 3% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        $data = array(
                            'subject' => 'Promotion to Rookie! - Solushop Ghana',
                            'name' => $sales_associate->first_name,
                            'message' => "Congrats on your first sale. You are now a Rookie sales associate. Keep selling to become a Veteran and enjoy 3% commission on all orders."
                        );
            
                        Mail::to($sales_associate->email, $sales_associate->first_name)
                            ->queue(new Alert($data));
                    }

                    $sales_associate->save();
                }

                //update order
                Order::where('id', $orderID)
                    ->update([
                        "order_state" => 3,
                    ]);

                /*--- Notify Customer ---*/
                $sms = new SMS;
                $sms->sms_message = "Hi ".$order["customer"]["first_name"]." your order $orderID has been confirmed and is being processed.";
                $sms->sms_phone = $order["customer"]["phone"];
                $sms->sms_state = 1;
                $sms->save();

                $data = array(
                    'subject' => 'Order Confirmed - Solushop Ghana',
                    'name' => $order["customer"]["first_name"],
                    'message' => "Your order $orderID has been confirmed and is being processed."
                );
    
                Mail::to($order["customer"]["email"], $order["customer"]["first_name"])
                    ->queue(new Alert($data));

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Payment Receipt Confirmation';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." confirmed payment receipt for order ".$orderID);
                
                return redirect()->back()->with("success_message", "Order payment receipt confirmed.");

                break;

            case 'confirm_order':
                $order = Order::
                        where("id", $orderID)
                        ->with('order_items.sku.product.images', 'order_items.sku.product.vendor', 'order_items.order_item_state', 'customer', 'order_state', 'address', 'coupon.sales_associate.badge_info')
                        ->first()
                        ->toArray();

                /*--- Update Order and Order Items ---*/
                //update order
                Order::where('id', $orderID)
                    ->update([
                        "order_state" => 3,
                    ]);

                //update order
                OrderItem::where('oi_order_id', $orderID)
                    ->update([
                        "oi_state" => 2,
                    ]);
                 /*--- Accrue to Sales Associate if Coupon was used | Record Transaction | Promote Sales associate where necessary ---*/
                 if (isset($order["order_scoupon"]) AND $order["order_scoupon"] != NULL AND $order["order_scoupon"] != "NULL") {
                    //sales associate old total sales
                    $ots = Order::
                        where("order_scoupon", $order["order_scoupon"])
                        ->whereIn("order_state", [3, 4, 5, 6])
                        ->sum('order_subtotal');

                    //sales associate new total sales
                    $nts = $ots + $order["order_subtotal"];

                    $sales_associate = SalesAssociate::
                        where('id', $order["coupon"]["sales_associate"]["id"])
                        ->first();

                    //update sales associate balance
                    $sales_associate->balance += round($order["coupon"]["sales_associate"]["badge_info"]["sab_commission"] * $order["order_subtotal"], 2);

                    //record transaction
                    $transaction = new AccountTransaction;
                    $transaction->trans_type                = "Sales Associate Accrual";
                    $transaction->trans_amount              = round($order["coupon"]["sales_associate"]["badge_info"]["sab_commission"] * $order["order_subtotal"], 2);
                    $transaction->trans_credit_account_type = 1;
                    $transaction->trans_credit_account      = "INT-SC001";
                    $transaction->trans_debit_account_type  = 7;
                    $transaction->trans_debit_account       = $order["coupon"]["sales_associate"]["id"];
                    $transaction->trans_description         = $log = "Accrual of GH¢ ".round($order["coupon"]["sales_associate"]["badge_info"]["sab_commission"] * $order["order_subtotal"], 2)." to ".$sales_associate->first_name." ".$sales_associate->last_name." for order $orderID";
                    $transaction->trans_date                = date("Y-m-d G:i:s");
                    $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                    $transaction->save();

                    //notify sales associate of update
                    $sms = new SMS;
                    $sms->sms_message = "Hi ".$sales_associate->first_name.", order $orderID made with your coupon has been confirmed. Your new balance is GHS ".$sales_associate->balance;
                    $sms->sms_phone = $sales_associate->phone;
                    $sms->sms_state = 1;
                    $sms->save();

                    $data = array(
                        'subject' => 'S.A. Order Confirmed - Solushop Ghana',
                        'name' => $sales_associate->first_name,
                        'message' => "Order $orderID made with your coupon has been confirmed. Your new balance is GHS ".$sales_associate->balance
                    );
        
                    Mail::to($sales_associate->email, $sales_associate->first_name)
                        ->queue(new Alert($data));

                    //promote where necessary
                    if ($ots < 20000 && $nts >= 20000) {
                        //promote to elite
                        $sales_associate->badge = 4;

                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name.", you are now an Elite Sales Associate. You can now enjoy 4% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        $data = array(
                            'subject' => 'Promotion to Elite! - Solushop Ghana',
                            'name' => $sales_associate->first_name,
                            'message' => "Congrats! You are now an Elite Sales Associate. You can now enjoy 4% commission on all orders."
                        );
            
                        Mail::to($sales_associate->email, $sales_associate->first_name)
                            ->queue(new Alert($data));

                    }elseif($ots < 5000 && $nts >= 5000){
                        //promote to veteran
                        $sales_associate->badge = 3;
                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name.", you are now an Veteran Sales Associate. You can now enjoy 3% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        $data = array(
                            'subject' => 'Promotion to Veteran! - Solushop Ghana',
                            'name' => $sales_associate->first_name,
                            'message' => "Congrats! You are now an Veteran Sales Associate. You can now enjoy 3% commission on all orders."
                        );
            
                        Mail::to($sales_associate->email, $sales_associate->first_name)
                            ->queue(new Alert($data));

                    }elseif($ots == 0 && $nts > 0){
                        //promote to rookie
                        $sales_associate->badge = 2;
                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name." on your first sale. You are now a Rookie sales associate. Keep selling to become a Veteran and enjoy 3% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        $data = array(
                            'subject' => 'Promotion to Rookie! - Solushop Ghana',
                            'name' => $sales_associate->first_name,
                            'message' => "Congrats on your first sale. You are now a Rookie sales associate. Keep selling to become a Veteran and enjoy 3% commission on all orders."
                        );
            
                        Mail::to($sales_associate->email, $sales_associate->first_name)
                            ->queue(new Alert($data));

                    }

                    $sales_associate->save();
                }

                //update order
                Order::where('id', $orderID)
                    ->update([
                        "order_state" => 3,
                    ]);

                /*--- Notify Customer ---*/
                $sms = new SMS;
                $sms->sms_message = "Hi ".$order["customer"]["first_name"]." your order $orderID has been confirmed and is being processed.";
                $sms->sms_phone = $order["customer"]["phone"];
                $sms->sms_state = 1;
                $sms->save();

                $data = array(
                    'subject' => 'Order Confirmed - Solushop Ghana',
                    'name' => $order["customer"]["first_name"],
                    'message' => "Your order $orderID has been confirmed and is being processed."
                );
    
                Mail::to($order["customer"]["email"], $order["customer"]["first_name"])
                    ->queue(new Alert($data));

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Payment Receipt Confirmation';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." confirmed order ".$orderID);
                
                return redirect()->back()->with("success_message", "Order confirmed.");
                break;

            case 'cancel_order_no_refund':
                /*--- Update Order Items---*/
                OrderItem::where([
                    ['oi_order_id', '=', $orderID]
                ])->update([
                    'oi_state' => 5
                ]);
            
                /*--- Update Order ---*/
                Order::where([
                    ['id', '=', $orderID]
                ])
                ->update([
                    'order_state' => 7
                ]);

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Cancellation (No Refund)';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." cancelled order $orderID.");
                
                return redirect()->back()->with("success_message", "Order cancelled successfully.");
                break;

            case 'cancel_order_partial_refund':
                /*--- Update Order Items---*/
                OrderItem::where([
                    ['oi_order_id', '=', $orderID]
                ])->update([
                    'oi_state' => 5
                ]);
            
                /*--- Update Order ---*/
                Order::where([
                    ['id', '=', $orderID]
                ])
                ->update([
                    'order_state' => 7
                ]);

                $order = Order::where('id', $orderID)->first();

                /*--- Deduct from main account ---*/
                $count = Count::first();
                if(isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL"){
                    $count->account -= 0.99 * $order->order_subtotal;
                }else{
                    $count->account -= $order->order_subtotal;
                }
                $count->save();

                /*--- Top up customer ---*/
                $customer = Customer::where('id', $order->order_customer_id)->with('milk', 'chocolate')->first();

                if(isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL"){
                    $newCustomerBalance     = round((($customer->milk->milk_value * $customer->milkshake) - $customer->chocolate->chocolate_value) + (0.99 *  $order->order_subtotal), 2);
                }else{
                    $newCustomerBalance     = round((($customer->milk->milk_value * $customer->milkshake) - $customer->chocolate->chocolate_value) + $order->order_subtotal, 2);
                }
                $newCustomerMilkshake   = ($newCustomerBalance + $customer->chocolate->chocolate_value) / $customer->milk->milk_value;
                $customer->milkshake    = $newCustomerMilkshake;

                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Partial Order Refund";
                if (isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL") {
                    $transaction->trans_amount = round(0.99 * $order->order_subtotal, 2);
                } else {
                    $transaction->trans_amount = $order->order_subtotal;
                }
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 5;
                $transaction->trans_debit_account       = $customer->id;
                if (isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL") {
                    $transaction->trans_description         = $log = "Partial Refund of GH¢ ".(0.99 * $order->order_subtotal)." to ".$customer->first_name." ".$customer->last_name." for order $orderID";
                } else {
                    $transaction->trans_description         = $log = "Partial Refund of GH¢ ".$order->order_subtotal." to ".$customer->first_name." ".$customer->last_name." for order $orderID";
                }
                
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $transaction->save();
                /*--- Notify customer ---*/
                $sms_message="Sorry ".$customer->first_name.", your order $orderID has been cancelled. A refund of GHS ";
                if (isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL") {
                    $sms_message .= (0.99 * $order->order_subtotal);
                } else {
                    $sms_message .= $order->order_subtotal;
                }
                $sms_message .= " has been done to your Solushop Wallet. We apologize for any inconvenience caused.";
                $email_message = $sms_message;
                $sms = new SMS;
                $sms->sms_message = $sms_message;
                $sms->sms_phone = $customer->phone;
                $sms->sms_state = 1;
                $sms->save();

                $data = array(
                    'subject' => 'Order Cancelled - Solushop Ghana',
                    'name' => $customer->first_name,
                    'message' => $email_message
                );
    
                Mail::to($customer->email, $customer->first_name)
                    ->queue(new Alert($data));

                $order->save();
                $customer->save();
                

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Cancellation (Partial Refund)';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." cancelled order $orderID.");

                return redirect()->back()->with("success_message", "Order cancelled successfully.");
                break;

            case 'cancel_order_full_refund':
                /*--- Update Order Items---*/
                OrderItem::where([
                    ['oi_order_id', '=', $orderID]
                ])->update([
                    'oi_state' => 5
                ]);
            
                /*--- Update Order ---*/
                Order::where([
                    ['id', '=', $orderID]
                ])
                ->update([
                    'order_state' => 7
                ]);

                $order = Order::where('id', $orderID)->first();

                /*--- Deduct from main account ---*/
                $count = Count::first();
                if(isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL"){
                    $count->account -= 0.99 * $order->order_subtotal + $order->order_shipping;
                }else{
                    $count->account -= $order->order_subtotal + $order->order_shipping;
                }
                $count->save();

                /*--- Top up customer ---*/
                $customer = Customer::where('id', $order->order_customer_id)->with('milk', 'chocolate')->first();

                if(isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL"){
                    $newCustomerBalance     = round((($customer->milk->milk_value * $customer->milkshake) - $customer->chocolate->chocolate_value) + (0.99 *  $order->order_subtotal + $order->order_shipping), 2);
                }else{
                    $newCustomerBalance     = round((($customer->milk->milk_value * $customer->milkshake) - $customer->chocolate->chocolate_value) + $order->order_subtotal + $order->order_shipping, 2);
                }
                $newCustomerMilkshake   = ($newCustomerBalance + $customer->chocolate->chocolate_value) / $customer->milk->milk_value;
                $customer->milkshake    = $newCustomerMilkshake;

                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Full Order Refund";
                if (isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL") {
                    $transaction->trans_amount = round(0.99 * $order->order_subtotal + $order->order_shipping, 2);
                } else {
                    $transaction->trans_amount = $order->order_subtotal + $order->order_shipping;
                }
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 5;
                $transaction->trans_debit_account       = $customer->id;
                if (isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL") {
                    $transaction->trans_description         = $log = "Full Refund of GH¢ ".(0.99 * $order->order_subtotal + $order->order_shipping)." to ".$customer->first_name." ".$customer->last_name." for order $orderID";
                } else {
                    $transaction->trans_description         = $log = "Full Refund of GH¢ ".$order->order_subtotal + $order->order_shipping." to ".$customer->first_name." ".$customer->last_name." for order $orderID";
                }
                
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $transaction->save();

                /*--- Notify customer ---*/
                $sms_message="Hi ".$customer->first_name.", your order $orderID has been cancelled. A full refund of GHS ";
                if (isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL") {
                    $sms_message .= (0.99 * $order->order_subtotal) + $order->order_shipping;
                } else {
                    $sms_message .= $order->order_subtotal + $order->order_shipping;
                }
                $sms_message .= " has been done to your Solushop Wallet. We apologize for any inconvenience caused.";
                $sms = new SMS;
                $sms->sms_message = $email_message = $sms_message;
                $sms->sms_phone = $customer->phone;
                $sms->sms_state = 1;
                $sms->save();

                $data = array(
                    'subject' => 'Order Cancelled - Solushop Ghana',
                    'name' => $customer->first_name,
                    'message' => $email_message
                );
    
                Mail::to($customer->email, $customer->first_name)
                    ->queue(new Alert($data));

                $order->save();
                $customer->save();
                

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Cancellation (Full Refund)';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." cancelled order $orderID.");

                return redirect()->back()->with("success_message", "Order cancelled successfully.");
                break;

            case 'record_shipping':
                /*--- Record shipping charge on order ---*/
                $order = Order::where('id', $orderID)->first();
                $order->dp_shipping = $request->shipping_amount;
                $order->save();

                /*--- Accrue shipping charge to partner ---*/
                if ($request->shipping_amount > 0) {
                    $partner = DeliveryPartner::where('id', $request->delivery_partner)->first();
                    $partner->balance += $request->shipping_amount;

                    $transaction = new AccountTransaction;
                    $transaction->trans_type                = "Delivery Partner Accrual";
                    $transaction->trans_amount              = $request->shipping_amount;
                    $transaction->trans_credit_account_type = 1;
                    $transaction->trans_credit_account      = "INT-SC001";
                    $transaction->trans_debit_account_type  = 9;
                    $transaction->trans_debit_account       = $partner->id;
                    $transaction->trans_description         = $log = "Accrual of GH¢ ".$request->shipping_amount." to ".$partner->first_name." ".$partner->last_name." for order $orderID";
                    $transaction->trans_date                = date("Y-m-d G:i:s");
                    $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                    $transaction->save();

                    $partner->save();
                }

                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Shipping Charge Entered';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." recorded an ".$log);
                
                return redirect()->back()->with("success_message", "Shipping charge recorded successfully.");

                break;
            
            default:
                # code...
                break;
        }
    }

    public function showMessages(){

        $conversations = Conversation::all()->toArray();
        for ($i=0; $i < sizeof($conversations); $i++) { 
            $conversation_key = explode("|", $conversations[$i]["conv_key"]);

            $conversations[$i]["customer"] = Customer::where([
                ['id', "=", trim($conversation_key[0])]
            ])->get()->toArray();
            
            $conversations[$i]["vendor"] = Vendor::where('id', trim($conversation_key[1]))->first()->toArray();
        }
        
        return view('portal.main.manager.conversations')
            ->with("conversations", $conversations);
    }

    public function showFlaggedMessages(){
        $messages["type"] = "Flagged";
        $messages["all"] = MessageFlag::with('message')->get()->toArray();
        for ($i=0; $i < sizeof($messages["all"]); $i++) { 
            if (substr($messages["all"][$i]["message"]["message_sender"], 0, 1) == "C") {
                //customer
                $sender = Customer::where('id', $messages["all"][$i]["message"]["message_sender"])->first()->toArray();
                $messages["all"][$i]["message"]["sender"] = $sender["first_name"]." ".$sender["last_name"];
            }elseif(substr($messages["all"][$i]["message"]["message_sender"], 0, 1) == "M"){
                $messages["all"][$i]["message"]["sender"] = "Solushop Management";
            }else{
                //vendor
                $messages["all"][$i]["message"]["sender"] = Vendor::where('id', $messages["all"][$i]["message"]["message_sender"])->get('name');
            }
        }

        return view("portal.main.manager.messages")
            ->with("messages", $messages);
    }

    public function processFlaggedMessages(Request $request){
        switch ($request->message_action) {
            case 'approve':
                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Flagged Message Approved';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." approved flagged message ".$request->message_id);
                
                
                /*--- Delete Flag ---*/
                MessageFlag::where('mf_mid', $request->message_id)->delete();

                return redirect()->back()->with("success_message", "Message Approved.");
                break;

            case 'delete':
                /*--- Store Message details in deleted messages ---*/
                $message = Message::where('id', $request->message_id)->first();
                $deleted_message = new DeletedMessage;
                $deleted_message->message_sender = $message->message_sender;
                $deleted_message->message_content = $message->message_content;
                $deleted_message->message_conversation_id = $message->message_conversation_id;
                $deleted_message->message_timestamp = $message->message_timestamp;
                $deleted_message->message_read = $message->message_read;
                $deleted_message->save();
                
                /*--- Update Message Contents and Sender ---*/
                $message->message_content = "This message has been deleted by Management because it does not conform to the TnCs for communication on Solushop.";
                $message->save();

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Flagged Message Deleted';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." deleted flagged message ".$request->message_id);
                
                
                /*--- Delete Flag ---*/
                MessageFlag::where('mf_mid', $request->message_id)->delete();

                return redirect()->back()->with("success_message", "Message Deleted.");
                break;
            
            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }
    }

    public function showDeletedMessages(){
        $messages["type"] = "Deleted";
        $messages["all"] = DeletedMessage::get()->toArray();
        for ($i=0; $i < sizeof($messages["all"]); $i++) { 
            if (substr($messages["all"][$i]["message_sender"], 0, 1) == "C") {
                //customer
                $sender = Customer::where('id', $messages["all"][$i]["message_sender"])->first()->toArray();
                $messages["all"][$i]["sender"] = $sender["first_name"]." ".$sender["last_name"];
            }elseif(substr($messages["all"][$i]["message_sender"], 0, 1) == "M"){
                $messages["all"][$i]["sender"] = "Solushop Management";
            }else{
                //vendor
                $messages["all"][$i]["sender"] = Vendor::where('id', $messages["all"][$i]["message_sender"])->get('name');
            }
        }

        return view("portal.main.manager.messages")
            ->with("messages", $messages);
    }

    public function showConversation($conversationID){
        /* Get conversation details */
        $conversation["record"] = Conversation::where('id', $conversationID)->first()->toArray();
        $conversation["participant_ids"] = explode("|", $conversation["record"]["conv_key"]);
        $conversation["customer"] = Customer::where('id', $conversation["participant_ids"][0])->first()->toArray();
        $conversation["vendor"] = Vendor::where('id', $conversation["participant_ids"][1])->first()->toArray();
        /* Get conversation messages */
        $conversation["messages"] = Message::where('message_conversation_id', $conversationID)->get()->toArray();
        

        return view("portal.main.manager.view-conversation")
            ->with("conversation", $conversation);
    }

    public function processConversation(Request $request, $conversationID){
         
        $message = new Message;
        $message->message_sender = "MGMT";
        $message->message_content = $request->message;
        $message->message_conversation_id = $conversationID;
        $message->message_timestamp = date("Y-m-d H:i:s");
        $message->message_read = "Init|";
        $message->save();

        /*--- log activity ---*/
        activity()
        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Management Message Sent';
        })
        ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." sent a message [".$request->message."] in conversation ID $conversationID");

        return redirect()->back()->with("success_message", "Message sent successfully.");
    }

    public function showProducts(){
        return view("portal.main.manager.products")
            ->with("products", Product::where([
                ["product_state", "<>", 4]
            ])
            ->with('skus', 'vendor', 'images', 'state')
            ->get()
            ->toArray());
    }

    public function processProducts(Request $request){
        $product = Product::where('id', $request->product_id)->first();
        switch ($request->product_action) {
            
            case 'approve':
                /*--- Check for subscription and allowance for new product ---*/
                if (is_null(VendorSubscription::where('vs_vendor_id', $product->product_vid)->with('package')->first()) OR VendorSubscription::where('vs_vendor_id', $product->product_vid)->with('package')->first()->vs_days_left < 1) {
                    return redirect()->back()->with("error_message", "Vendor has no active subscriptions. Please ask vendor to subscribe to be able to approve a product.");
                }elseif(Product::where('product_vid', $product->product_vid)->whereIn('product_state', [1])->get()->count() >= VendorSubscription::where('vs_vendor_id', $product->product_vid)->with('package')->first()->package->vs_package_product_cap){
                    return redirect()->back()->with("error_message", "Upload limit for vendor reached on current vendor subscription. Please upgrade to enable approve");
                }

                /*--- change product state ---*/
                Product::
                    where([
                        ['id', "=", $request->product_id]
                    ])->update([
                        'product_state' => 1
                    ]);
                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Approved';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." approved product ".$request->product_id);
                return redirect()->back()->with("success_message", "Product ".$request->product_id." approved successfully.");
                break;

            case 'reject':
                /*--- change product state ---*/
                Product::
                    where([
                        ['id', "=", $request->product_id]
                    ])->update([
                        'product_state' => 3
                    ]);
                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Rejected';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." rejected product ".$request->product_id);
                return redirect()->back()->with("success_message", "Product ".$request->product_id." rejected successfully.");
                break;

            case 'disapprove':
                /*--- change product state ---*/
                Product::
                    where([
                        ['id', "=", $request->product_id]
                    ])->update([
                        'product_state' => 2
                    ]);

                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Disapproved';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." disapproved product ".$request->product_id);
                return redirect()->back()->with("success_message", "Product ".$request->product_id." disapproved successfully.");
                break;

            case 'restore':
                $product = Product::where('id', $request->product_id)->first();
                

                /*--- Check for subscription and allowance for new product ---*/
                if (is_null(VendorSubscription::where('vs_vendor_id', $product->product_vid)->with('package')->first()) OR VendorSubscription::where('vs_vendor_id', $product->product_vid)->with('package')->first()->vs_days_left < 1) {
                    return redirect()->back()->with("error_message", "Vendor has no active subscriptions. Please ask vendor to subscribe to be able to restore a product.");
                }elseif(Product::where('product_vid', $product->product_vid)->whereIn('product_state', [1, 2, 3, 5])->get()->count() >= VendorSubscription::where('vs_vendor_id', $product->product_vid)->with('package')->first()->package->vs_package_product_cap){
                    return redirect()->back()->with("error_message", "Upload limit for vendor reached on current vendor subscription. Please upgrade to enable restore");
                }

                /*--- change product state ---*/
                Product::
                    where([
                        ['id', "=", $request->product_id]
                    ])->update([
                        'product_state' => 2
                    ]);

                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Restored';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." restored product ".$request->product_id);
                return redirect()->back()->with("success_message", "Product ".$request->product_id." restored successfully.");
                break;

            

            case 'delete':
                /*--- change product state ---*/
                Product::
                    where([
                        ['id', "=", $request->product_id]
                    ])->update([
                        'product_state' => 4
                    ]);
                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Deleted';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." deleted product ".$request->product_id);
                return redirect()->back()->with("success_message", "Product ".$request->product_id." deleted successfully.");
                break;
            
            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }
        
    }

    public function showDeletedProducts(){
        return view("portal.main.manager.deleted-products")
            ->with("products", Product::where([
                ["product_state", "=", 4]
            ])
            ->with('vendor', 'images', 'state')
            ->get()
            ->toArray());
    }


    public function showAddProduct(){
       /*--- Category Options ---*/
       $product["category_options"] = ProductCategory::orderBy('pc_description')->where('pc_level', 3)->get()->toArray();

       /*--- Vendor Options ---*/
       $product["vendor_options"] = Vendor::orderBy('name')->get()->toArray();

       return view("portal.main.manager.add-product")
           ->with("product", $product);
    }

    public function processAddProduct(Request $request){
        /*--- Validate Details ---*/
        /*--- Validate form data  ---*/
        $validator = Validator::make($request->all(), [
            'vendor' => 'required',
            'name' => 'required',
            'features' => 'required',
            'category' => 'required',
            'settlement_price' => 'required',
            'selling_price' => 'required',
            'discount' => 'required',
            'dd' => 'required',
            'dc' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            $messageType = "error_message";
            $messageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $messageContent .= $messages[0]." "; 
            }

            return redirect()->back()->with($messageType, $messageContent);
        }

        /*--- Check for subscription and allowance for new product ---*/
        if (is_null(VendorSubscription::where('vs_vendor_id', $request->vendor)->with('package')->first()) OR VendorSubscription::where('vs_vendor_id', $request->vendor)->with('package')->first()->vs_days_left < 1) {
            return redirect()->back()->with("error_message", "Vendor currently has no active subscriptions. Please ask vendor to subscribe to be able to upload a product.");
        }elseif(Product::where('product_vid', $request->vendor)->whereIn('product_state', [1, 2, 3, 5])->get()->count() >= VendorSubscription::where('vs_vendor_id', $request->vendor)->with('package')->first()->package->vs_package_product_cap){
            return redirect()->back()->with("error_message", "Upload limit for vendor reached on current vendor subscription. Please upgrade to continue uploading");
        }

        /*--- Validate Images ---*/
        for ($i=0; $i < sizeof($request->product_images); $i++) { 
            if($request->product_images[$i]->getClientOriginalExtension() != "jpg"){
                return back()->with("error_message", "Images must be of type jpg");
            }

            list($width, $height) = getimagesize($request->product_images[$i]);
            if ($width != $height or $height < 600) {
                return back()->with("error_message", "Images must be minimum height 600px with aspect ratio of 1");
            }

            if(filesize($request->product_images[$i]) > 5000000){
                return back()->with("error_message", "One or more images exceed the allowed size for upload.");
            }
        }

        /*--- Validate and generate Product Slug ---*/
        if((Product::where([
            ['product_vid', '=', $request->vendor],
            ['product_name', '=', $request->name]
        ])->get()->count()) > 0){
            $product_slug_count = Product::where([
                ['product_vid', '=', $request->vendor],
                ['product_name', '=', $request->name]
            ])->get()->count();
            $product_slug_count++;
            $product_slug = str_slug($request->name)."-".$product_slug_count;
        }else{
            $product_slug = str_slug($request->name);
        }

        /*--- Generate product id and set detail variables ---*/
        $count = Count::first();
        $count->product_count++;

        $product = New Product;
        $product_id = "P-".date("Ymd")."-".$count->product_count;
        $product->id = $product_id;
        $product->product_vid = $request->vendor;
        $product->product_name = ucwords(strtolower($request->name));
        $product->product_slug = $product_slug;
        $product->product_features = $request->features;
        $product->product_cid = $request->category;
        $product->product_settlement_price = $request->settlement_price;
        $product->product_selling_price = $request->selling_price;
        $product->product_discount = $request->discount;
        $product->product_dd = $request->dd;
        $product->product_dc = $request->dc;
        $product->product_description = $request->description;
        $product->product_tags = $request->tags;
        $product->product_type = $request->type;
        $product->product_state = 2;
        $product->product_views = 0;


        /*--- Save product stock --- */
        $count->sku_count++;

        $sku = new StockKeepingUnit;
        $sku->id                        = "S-".($count->sku_count);
        $sku->sku_product_id            = $product_id;
        $sku->sku_variant_description   = $request->input('variantDescription0');
        $sku->sku_selling_price         = $product->product_selling_price;
        $sku->sku_settlement_price      = $product->product_settlement_price;
        $sku->sku_discount              = $product->product_discount;
        $sku->sku_stock_left            = $request->input('stock0');
        $sku->save();

        for ($i=1; $i < $request->newSKUCount; $i++) { 
            if ((ucfirst(trim($request->input('variantDescription'.$i))) != "None") AND ($request->input('stock'.$i) >= 0)) {
                //insert sku
                $count->sku_count++;

                $sku = new StockKeepingUnit;
                $sku->id                        = "S-".($count->sku_count);
                $sku->sku_product_id            = $product_id;
                $sku->sku_variant_description   = $request->input('variantDescription'.$i);
                $sku->sku_selling_price         = $product->product_selling_price;
                $sku->sku_settlement_price      = $product->product_settlement_price;
                $sku->sku_discount              = $product->product_discount;
                $sku->sku_stock_left            = $request->input('stock'.$i);
                $sku->save();

            }
        }

        /*--- Save product images --- */
        for ($i=0; $i < sizeof($request->product_images); $i++) { 
                    
            $product_image = new ProductImage;
            $product_image->pi_product_id = $product_id;
            $product_image->pi_path = $product_id.rand(1000, 9999);

            $img = Image::make($request->product_images[$i]);

            //save original image
            $img->save('app/assets/img/products/original/'.$product_image->pi_path.'.jpg');

            //save main image
            $img->resize(600, 600);
            $img->insert('portal/images/watermark/stamp.png', 'center');
            $img->save('app/assets/img/products/main/'.$product_image->pi_path.'.jpg');

            //save thumbnail
            $img->resize(300, 300);
            $img->save('app/assets/img/products/thumbnails/'.$product_image->pi_path.'.jpg');

            //store image details
            $product_image->save();
        }


        /*--- Save product --- */
        $product->save();
        $count->save();

        /*--- log activity ---*/
        activity()
        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Product Added';
        })
        ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." added product ".$product_id);
        return redirect()->back()->with("success_message", "Product ".$product_id." added successfully.");

        
    }

    public function showProduct($productID){
        if (is_null(Product::where('id', $productID)->first())) {
            return redirect()->back()->with("error_message", "Product not found");
        }

        $product =  Product::
        where('id', $productID)
        ->with('images', 'skus', 'vendor', 'state')
        ->first()
        ->toArray();

        /*--- Build SKU array ---*/
        $sku_array = [];
        for ($i=0; $i < sizeof($product["skus"]); $i++) { 
            $sku_array[$i] = $product["skus"][$i]["id"];
        }


        /*--- Stats ---*/
        $product["stats"]["wishlist"] = WishlistItem::
            where('wi_product_id', $product["id"])
            ->count();

        $product["stats"]["cart"] = CartItem::
        whereIn('ci_sku', $sku_array)
        ->count();

        $product["stats"]["purchases"] = OrderItem::
        whereIn('oi_sku', $sku_array)
        ->whereIn('oi_state', [2, 3, 4])
        ->count();

        /*--- Category Options ---*/
        $product["category_options"] = ProductCategory::orderBy('pc_description')->where('pc_level', 3)->get()->toArray();

        /*--- Vendor Options ---*/
        $product["vendor_options"] = Vendor::orderBy('name')->get()->toArray();

        return view("portal.main.manager.product")
            ->with("product", $product);
    }

    public function processProduct(Request $request, $productID){
        switch ($request->product_action) {
            case 'update_details':
                /*--- Validate form data  ---*/
                $validator = Validator::make($request->all(), [
                    'vendor' => 'required',
                    'name' => 'required',
                    'features' => 'required',
                    'category' => 'required',
                    'settlement_price' => 'required',
                    'selling_price' => 'required',
                    'discount' => 'required',
                    'dd' => 'required',
                    'dc' => 'required',
                    'type' => 'required'
                ]);

                if ($validator->fails()) {
                    $messageType = "error_message";
                    $messageContent = "";

                    foreach ($validator->messages()->getMessages() as $field_name => $messages)
                    {
                        $messageContent .= $messages[0]." "; 
                    }

                    return redirect()->back()->with($messageType, $messageContent);
                }

                $product = Product::where('id', $productID)->first();

                /*--- Validate Product Slug ---*/
                if(trim(strtolower($request->name)) != trim(strtolower($product->product_name))){
                    if((Product::where([
                        ['product_vid', '=', $request->vendor],
                        ['product_name', '=', $request->name],
                        ['id', '<>', $productID]
                    ])->get()->count()) > 0){
                        $product_slug_count = Product::where([
                            ['product_vid', '=', $request->vendor],
                            ['product_name', '=', $request->name],
                            ['id', '<>', $productID]
                        ])->get()->count();
                        $product_slug_count++;
                        $product_slug = str_slug($request->name)."-".$product_slug_count;
                    }else{
                        $product_slug = str_slug($request->name);
                    }
                }else{
                    $product_slug = $product->product_slug;
                }


                /*--- Update Details ---*/
                
                $product->product_vid = $request->vendor;
                $product->product_name = ucwords(strtolower($request->name));
                $product->product_slug = $product_slug;
                $product->product_features = $request->features;
                $product->product_cid = $request->category;
                $product->product_settlement_price = $request->settlement_price;
                $product->product_selling_price = $request->selling_price;
                $product->product_discount = $request->discount;
                $product->product_dd = $request->dd;
                $product->product_dc = $request->dc;
                $product->product_description = $request->description;
                $product->product_tags = $request->tags;
                $product->product_type = $request->type;
                $product->save();


                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Details Updated';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." updated details of product ".$productID);
                return redirect()->back()->with("success_message", "Product ".$productID." details updated successfully.");
                break;

            case 'update_stock':
                /*--- update old stock ---*/
                for ($i=0; $i < $request->skuCount; $i++) { 
                    $sku = StockKeepingUnit::where('id', $request->input('sku'.$i))->first();
                    $sku->sku_stock_left = $request->input('stock'.$i);
                    $sku->save();
                }

                /*--- add new stock (if any) ---*/
                if ($request->newSKUCount > $request->skuCount) {
                     //select product
                     $product = Product::where('id', $productID)->first();
                    for ($i=$request->skuCount; $i < $request->newSKUCount; $i++) { 
                        if ((ucfirst(trim($request->input('variantDescription'.$i))) != "None") AND ($request->input('stock'.$i) >= 0)) {
                            //insert sku
                            $count = Count::first();
                            $count->sku_count++;

                            $sku = new StockKeepingUnit;
                            $sku->id                        = "S-".($count->sku_count);
                            $sku->sku_product_id            = $product->id;
                            $sku->sku_variant_description   = $request->input('variantDescription'.$i);
                            $sku->sku_selling_price         = $product->product_selling_price;
                            $sku->sku_settlement_price      = $product->product_settlement_price;
                            $sku->sku_discount              = $product->product_discount;
                            $sku->sku_stock_left            = $request->input('stock'.$i);
                            $sku->save();

                            //update count
                            $count->save();
                        }
                    }

                }

                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Stock Updated';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." updated stock of product ".$productID);
                return redirect()->back()->with("success_message", "Product ".$productID." stock updated successfully.");
                break;

            case 'delete_image':
                //select image
                $image = ProductImage::where('id', $request->image_id)->first();

                //delete files
                $main_image_path = "app/assets/img/products/main/";
                $thumbnail_image_path = "app/assets/img/products/thumbnails/";
                $original_image_path = "app/assets/img/products/original/";

                File::delete($main_image_path.$image->pi_path.'.jpg');
                File::delete($thumbnail_image_path.$image->pi_path.'.jpg');
                File::delete($original_image_path.$image->pi_path.'.jpg');

                //delete image
                $image->delete();


                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Image Deleted';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." deleted image ".$request->image_id." of product ".$productID);
                return redirect()->back()->with("success_message", "Image ".$request->image_id." deleted successfully.");
                break;
            
            case 'add_images':

                //validate images
                for ($i=0; $i < sizeof($request->product_images); $i++) { 
                    if($request->product_images[$i]->getClientOriginalExtension() != "jpg"){
                        return back()->with("error_message", "Images must be of type jpg");
                    }

                    list($width, $height) = getimagesize($request->product_images[$i]);
                    if ($width != $height or $height < 600) {
                        return back()->with("error_message", "Images must be minimum height 600px with aspect ratio of 1");
                    }

                    if(filesize($request->product_images[$i]) > 5000000){
                        return back()->with("error_message", "One or more images exceed the allowed size for upload.");
                    }
                }

                //process images
                for ($i=0; $i < sizeof($request->product_images); $i++) { 
                    
                    $product_image = new ProductImage;
                    $product_image->pi_product_id = $productID;
                    $product_image->pi_path = $productID.rand(1000, 9999);

                    $img = Image::make($request->product_images[$i]);

                    //save original image
                    $img->save('app/assets/img/products/original/'.$product_image->pi_path.'.jpg');

                    //save main image
                    $img->resize(600, 600);
                    $img->insert('portal/images/watermark/stamp.png', 'center');
                    $img->save('app/assets/img/products/main/'.$product_image->pi_path.'.jpg');

                    //save thumbnail
                    $img->resize(300, 300);
                    $img->save('app/assets/img/products/thumbnails/'.$product_image->pi_path.'.jpg');

                    //store image details
                    $product_image->save();
                }

                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Image(s) Uploaded';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." uploaded images for product ".$productID);
                return redirect()->back()->with("success_message", "Upload Successful.");
                break;

            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }
    }

    public function showVendors(){
        return view("portal.main.manager.vendors")
            ->with('vendors', Vendor::with('subscription.package')->get()->toArray());
    }

    public function showAddVendor(){
        return view("portal.main.manager.add-vendor");
    }

    public function processAddVendor(Request $request){
        /*--- Validate form data  ---*/
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'main_phone' => 'required|digits:10',
            'alt_phone' => 'required|digits:10',
            'mode_of_payment' => 'required',
            'payment_details' => 'required',
            'pick_up_address' => 'required', 
            'header_image' => 'required|image|dimensions:width=1305,height=360'
        ]);

        if ($validator->fails()) {
            $messageType = "error_message";
            $messageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $messageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput(['name', 'email', 'main_phone', 'alt_phone', 'mode_of_payment', 'payment_details', 'pick_up_address', 'header_image'])->with($messageType, $messageContent);
        }

        
        //check for username existence in system
        if (Vendor::where('username', str_slug($request->name, 2))->first()) {
            return redirect()->back()->withInput(['name', 'email', 'main_phone', 'alt_phone', 'mode_of_payment', 'payment_details', 'pick_up_address', 'header_image'])->with("error_message", "Name already associated with a Vendor");
        }

        //check for numbers being the same
        if ($request->main_phone == $request->alt_phone) {
            return redirect()->back()->withInput(['name', 'email', 'main_phone', 'alt_phone', 'mode_of_payment', 'payment_details', 'pick_up_address', 'header_image'])->with("error_message", "Main number cannot be the same as the alternate number");
        }



        /*--- Vendor ID ---*/
        $count = Count::first();
        $vendor_id = date("dmY").substr("00000".$count->vendor_count, strlen(strval($count->vendor_count)));

        /*--- save header file ---*/
        $header_file = $request->file('header_image');
        if ($header_file->getClientOriginalExtension() != "jpg") {
            return redirect()->back()->withInput(['name', 'email', 'main_phone', 'alt_phone', 'mode_of_payment', 'payment_details', 'pick_up_address', 'header_image'])->with("error_message", "Header image must be of type: .jpg");
        }

        $img = Image::make($header_file);
        $img->save('app/assets/img/vendor-banner/'.$vendor_id.'.jpg');

        

        /*--- store vendor data ---*/
        $vendor = new Vendor;
        $vendor->id                = $vendor_id;
        $vendor->name              = ucwords(strtolower($request->name));
        $vendor->username          = str_slug($vendor->name , '-');
        $vendor->phone             = "233".substr($request->main_phone, 1);
        $vendor->alt_phone         = "233".substr($request->alt_phone, 1);
        $vendor->email             = strtolower($request->email);
        $vendor->passcode          = $passcode = rand(100000, 999999);
        $vendor->password          = bcrypt($passcode);
        $vendor->address           = ucwords($request->pick_up_address);
        $vendor->mode_of_payment   = $request->mode_of_payment;
        $vendor->payment_details   = ucwords($request->payment_details);
        $vendor->balance           = 0;
        $vendor->save();


        /*--- update counts ---*/
        $count->vendor_count++;
        $count->save();
        
        /*--- notify vendor ---*/
        $sms = new SMS;
        $sms->sms_message = "Hi ".ucwords(strtolower($request->name)).", you have been accepted as a Vendor on Solushop. Subscribe to begin your journey with us.\n\nUsername: ".str_slug($request->name, '-')."\nPassword : $passcode\nLogin here : https://www.solushop.com.gh/portal/vendor";
        $sms->sms_phone = "233".substr($request->main_phone, 1);
        $sms->sms_state = 1;
        $sms->save();

        $data = array(
            'subject' => 'Confirmed Vendor - Solushop Ghana',
            'name' => ucwords(strtolower($request->name)),
            'message' => "You have been accepted as a Vendor on Solushop. Subscribe to begin your journey with us.<br><br>Username: ".str_slug($request->name, '-')."<br>Password : $passcode<br>Login here : <a href='https://www.solushop.com.gh/portal/vendor'>Solushop Vendor Portal</a>"
        );

        Mail::to(strtolower($request->email), ucwords(strtolower($request->name)))
            ->queue(new Alert($data));


         /*--- log activity ---*/
         activity()
         ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
         ->tap(function(Activity $activity) {
             $activity->subject_type = 'System';
             $activity->subject_id = '0';
             $activity->log_name = 'Vendor Registration';
         })
         ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." added ".ucwords(strtolower($request->name))." as a vendor");
         
         return redirect()->back()->with("success_message", ucwords(strtolower($request->name))." added successfully as a vendor.");
    }

    public function showVendor($vendorSlug){
         if (is_null(Vendor::where('username', $vendorSlug)->first())) {
            return redirect()->route("manager.show.vendors")->with("error_message", "Vendor not found.");
        }

        $vendor = Vendor::with('subscription.package')->where('username', $vendorSlug)->first()->toArray();

        $vendor["transactions"] = AccountTransaction::where([
                ['trans_credit_account_type', '=', '3'],
                ['trans_credit_account', '=', $vendor["id"]]
            ])->orWhere(
                [
                    ['trans_credit_account_type', '=', '4'],
                    ['trans_credit_account', '=', $vendor["id"]]
            ])->orWhere(
                [
                    ['trans_debit_account_type', '=', '3'],
                    ['trans_debit_account', '=', $vendor["id"]]
            ])->orWhere(
                [
                    ['trans_debit_account_type', '=', '4'],
                    ['trans_debit_account', '=', $vendor["id"]]
            ])
            ->get()
            ->toArray();
        return view("portal.main.manager.vendor")
            ->with('vendor', $vendor);
    }

    public function processVendor(Request $request, $vendorSlug){
        //select sales associates details
        $vendor = Vendor::where('username', $vendorSlug)->first();
        
        switch ($request->vendor_action) {
            case 'update_details':
                /*--- Validate form data  ---*/
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email',
                    'main_phone' => 'required|digits:10',
                    'alt_phone' => 'required|digits:10',
                    'mode_of_payment' => 'required',
                    'payment_details' => 'required',
                    'pick_up_address' => 'required', 
                    'header_image' => 'image|dimensions:width=1305,height=360'
                ]);

                if ($validator->fails()) {
                    $messageType = "error_message";
                    $messageContent = "";

                    foreach ($validator->messages()->getMessages() as $field_name => $messages)
                    {
                        $messageContent .= $messages[0]." "; 
                    }

                    return redirect()->back()->withInput(['name', 'email', 'main_phone', 'alt_phone', 'mode_of_payment', 'payment_details', 'pick_up_address', 'header_image'])->with($messageType, $messageContent);
                }

                
                //check for username existence in system
                if (Vendor::where([
                        ['username', "=", str_slug($request->name, 2)],
                        ['id', '<>', $vendor->id]
                    ])->first()) {
                    return redirect()->back()->withInput(['name', 'email', 'main_phone', 'alt_phone', 'mode_of_payment', 'payment_details', 'pick_up_address', 'header_image'])->with("error_message", "Name already associated with a Vendor");
                }

                //check for numbers being the same
                if ($request->main_phone == $request->alt_phone) {
                    return redirect()->back()->withInput(['name', 'email', 'main_phone', 'alt_phone', 'mode_of_payment', 'payment_details', 'pick_up_address', 'header_image'])->with("error_message", "Main number cannot be the same as the alternate number");
                }

                //check and update header if it is set
                if ($request->hasFile('header_image')) {
                    /*--- save header file ---*/
                    $header_file = $request->file('header_image');
                    if ($header_file->getClientOriginalExtension() != "jpg") {
                        return redirect()->back()->withInput(['name', 'email', 'main_phone', 'alt_phone', 'mode_of_payment', 'payment_details', 'pick_up_address', 'header_image'])->with("error_message", "Header image must be of type: .jpg");
                    }

                    $img = Image::make($header_file);
                    $img->save('app/assets/img/vendor-banner/'.$vendor->id.'.jpg');
                }


                //update details
                $vendor->name              = ucwords(strtolower($request->name));
                $vendor->username          = $updatedVendorSlug = str_slug($vendor->name , '-');
                $vendor->phone             = "233".substr($request->main_phone, 1);
                $vendor->alt_phone         = "233".substr($request->alt_phone, 1);
                $vendor->email             = strtolower($request->email);
                $vendor->address           = ucwords($request->pick_up_address);
                $vendor->mode_of_payment   = $request->mode_of_payment;
                $vendor->payment_details   = ucwords($request->payment_details);


                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Vendor Details Update';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." updated the details of vendor, ".$vendor->name);

                $success_message = $vendor->name."'s details updated successfully.";
                $vendor->save();
                
                return redirect()->route("manager.show.vendor", $updatedVendorSlug)->with("success_message", $success_message);
                break;

            case 'record_transaction':
                
                switch ($request->transaction_type) {
                    case 'Pay-Out':
                        /*--- Record transaction ---*/
                        $transaction = new AccountTransaction;
                        $transaction->trans_type                = "Vendor Payout";
                        $transaction->trans_amount              = $request->pay_out_amount;
                        $transaction->trans_credit_account_type = 1;
                        $transaction->trans_credit_account      = "INT-SC001";
                        $transaction->trans_debit_account_type  = 4;
                        $transaction->trans_debit_account       = $vendor->id;
                        $transaction->trans_description         = "Payout of GH¢ ".$request->pay_out_amount." to ".$vendor->name;
                        $transaction->trans_date                = date("Y-m-d G:i:s");
                        $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                        $transaction->save();

                        /*--- Update Vendor Balance ---*/
                        $vendor->balance -= $request->pay_out_amount;

                        /*--- Update Main Account Balance ---*/
                        $counts = Count::first();
                        $counts->account = round($counts->account - $request->pay_out_amount, 2);
                        $counts->save();

                        /*--- Notify vendor ---*/
                        $sms = new SMS;
                        $sms->sms_message = "Dear ".$vendor->name.", a payout of GHS ".$request->pay_out_amount." has been recorded to you. Your new balance is GHS ".$vendor->balance;
                        $sms->sms_phone = $vendor->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        /*--- Notify Vendor via Mail ---*/
                        $data = array(
                            'subject' => 'Vendor Payout - Solushop Ghana',
                            'name' => $vendor->name,
                            'message' => "A payout of GHS ".$request->pay_out_amount." has been recorded to you. Your new balance is GHS ".$vendor->balance
                        );

                        Mail::to($vendor->email, $vendor->name)
                            ->queue(new Alert($data));

                        /*--- log activity ---*/
                        activity()
                        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                        ->tap(function(Activity $activity) {
                            $activity->subject_type = 'System';
                            $activity->subject_id = '0';
                            $activity->log_name = 'Vendor Payout';
                        })
                        ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." recorded a payout of GH¢ ".$request->pay_out_amount." to vendor, ".$vendor->name);

                        $success_message = "Payout of GH¢ ".$request->pay_out_amount." to ".$vendor->name." recorded successfully.";
                        break;

                    case 'Penalty':
                        /*--- Record transaction ---*/
                        $transaction = new AccountTransaction;
                        $transaction->trans_type                = "Vendor Penalty";
                        $transaction->trans_amount              = $request->pay_out_amount;
                        $transaction->trans_credit_account_type = 3;
                        $transaction->trans_credit_account      = $vendor->id;
                        $transaction->trans_debit_account_type  = 1;
                        $transaction->trans_debit_account       = "INT-SC001";
                        $transaction->trans_description         = "Penalty of GH¢ ".$request->pay_out_amount." to ".$vendor->name;
                        $transaction->trans_date                = date("Y-m-d G:i:s");
                        $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                        $transaction->save();

                        /*--- Update Vendor Balance ---*/
                        $vendor->balance -= $request->pay_out_amount;

                        /*--- Notify vendor ---*/
                        $sms = new SMS;
                        $sms->sms_message = "Dear ".$vendor->name.", a penalty of GHS ".$request->pay_out_amount." has been recorded to you. Your new balance is GHS ".$vendor->balance;
                        $sms->sms_phone = $vendor->phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        $data = array(
                            'subject' => 'Vendor Penalty - Solushop Ghana',
                            'name' => $vendor->name,
                            'message' => "A penalty of GHS ".$request->pay_out_amount." has been recorded to you. Your new balance is GHS ".$vendor->balance
                        );

                        Mail::to($vendor->email, $vendor->name)
                            ->queue(new Alert($data));

                        /*--- log activity ---*/
                        activity()
                        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                        ->tap(function(Activity $activity) {
                            $activity->subject_type = 'System';
                            $activity->subject_id = '0';
                            $activity->log_name = 'Vendor Penalty';
                        })
                        ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." recorded a penalty of GH¢ ".$request->pay_out_amount." to vendor, ".$vendor->name);

                        $success_message = "Penalty of GH¢ ".$request->pay_out_amount." to ".$vendor->name." recorded successfully.";
                        break;
                    
                    default:
                        # code...
                        break;
                }
                
                $vendor->save();
                
                return redirect()->back()->with("success_message", $success_message);
                
                break;
            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }
    }

    public function showPickupHistory(){
        return view('portal.main.manager.pick-up-history')
                ->with('picked_up_items',  PickedUpItem::with('order_item')->get()->toArray());
    }

    public function showActivePickups(){
        return view('portal.main.manager.pick-ups')
                ->with('pick_up_items',  OrderItem::whereIn('oi_state', [2])->with("sku.product.images", "sku.product.vendor")->get()->toArray());
    }

    public function processActivePickups(Request $request){
        switch ($request->pick_up_action) {
            case 'mark_item':
                /*--- Change order Item State ---*/
                OrderItem::
                    where([
                        ['id', '=', $request->picked_up_item_id]
                    ])
                    ->update([
                        'oi_state' => 3,
                    ]);
                
                $order_item = OrderItem::where('id', $request->picked_up_item_id)->first()->toArray();
                $order = Order::where('id', $order_item['oi_order_id'])->with('customer')->first()->toArray();

                /*--- Change Order State (where necessary) ---*/
                $order_items_count = OrderItem::where('oi_order_id', $order_item['oi_order_id'])->get()->count();
                $picked_up_order_items_count = OrderItem::where('oi_order_id', $order_item['oi_order_id'])->whereIn('oi_state', [3, 4])->get()->count();
                

                if ($order_items_count == $picked_up_order_items_count) {
                    Order::
                    where([
                        ['id', '=', $order_item['oi_order_id']]
                    ])
                    ->update([
                        'order_state' => 4,
                    ]);
                }

                /*--- Notify Customer ---*/
                $sms = new SMS;
                $sms->sms_message = "Hi ".$order["customer"]["first_name"]." your ordered item, ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." has been picked up and is ready for delivery.";
                $sms->sms_phone = $order["customer"]["phone"];
                $sms->sms_state = 1;
                $sms->save();

                $data = array(
                    'subject' => 'Order Item Picked Up - Solushop Ghana',
                    'name' => $order["customer"]["first_name"],
                    'message' => "Your ordered item, ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." has been picked up and is ready for delivery."
                );

                Mail::to($order["customer"]["email"], $order["customer"]["first_name"])
                    ->queue(new Alert($data));

                /*--- Record Pickup History ---*/
                $picked_up_item = new PickedUpItem;
                $picked_up_item->pui_order_item_id          = $order_item["id"];
                $picked_up_item->pui_marked_by_id           = Auth::guard('manager')->user()->id;
                $picked_up_item->pui_marked_by_description  = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $picked_up_item->save();

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Item Picked Up';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." marked ordered item [ ".$order_item["id"]." ] ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." as picked up.");

                /*--- Return with success message ---*/
                return redirect()->back()->with("success_message", $order_item["oi_quantity"]." ".$order_item["oi_name"]." marked as picked up successfully.");
                break;

            case 'download_pick_up_guide':
                //get order items information
                $data["pick_ups"] = OrderItem::orderBy('oi_name', 'asc')->where('oi_state', 2)->with('sku.product.vendor')->get()->toArray();

                //get vendor information
                $data["vendors"] =  DB::select(
                    "SELECT distinct vendors.id, vendors.phone, vendors.alt_phone, vendors.name, vendors.address from vendors, order_items, stock_keeping_units, products where oi_state = '2' and order_items.oi_sku = stock_keeping_units.id and stock_keeping_units.sku_product_id = products.id and products.product_vid = vendors.id order by vendors.name"
                );

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Pick-Up Guide Download';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." downloaded Pick-Up Guide ".date('m-d-Y').".pdf");

                $pdf = PDF::loadView('portal.guides.pick-up', array('data' => $data));
                return $pdf->download('Pick-Up Guide '.date('m-d-Y').'.pdf');

                break;
            
            default:
                return redirect()->back()->with("error_message", "Something went wrong, please try again.");
                break;
        }
    }

    public function showDeliveryHistory(){
        return view('portal.main.manager.delivery-history')
                ->with('delivered_items',  DeliveredItem::with('order_item')->get()->toArray());
    }

    public function showActiveDeliveries(){
        return view('portal.main.manager.deliveries')
                ->with('delivery_items',  OrderItem::whereIn('oi_state', [2, 3])->with("sku.product.images")->get()->toArray());
    }

    public function processActiveDeliveries(Request $request){
        switch ($request->delivery_action) {
            case 'mark_item':
                /*--- Change order Item State ---*/
                OrderItem::
                    where([
                        ['id', '=', $request->delivered_item_id]
                    ])
                    ->update([
                        'oi_state' => 4,
                    ]);
                
                $order_item = OrderItem::where('id', $request->delivered_item_id)->with('sku.product.vendor')->first()->toArray();
                $order = Order::where('id', $order_item['oi_order_id'])->with('customer')->first()->toArray();

                /*--- Change Order State (where necessary) ---*/
                $order_items_count = OrderItem::where('oi_order_id', $order_item['oi_order_id'])->get()->count();
                $delivered_order_items_count = OrderItem::where('oi_order_id', $order_item['oi_order_id'])->whereIn('oi_state', [4])->get()->count();
                

                if ($order_items_count == $delivered_order_items_count) {
                    Order::
                    where([
                        ['id', '=', $order_item['oi_order_id']]
                    ])
                    ->update([
                        'order_state' => 6,
                    ]);
                }

                /*--- Notify Customer ---*/
                $sms = new SMS;
                $sms->sms_message = "Hi ".$order["customer"]["first_name"]." your ordered item, ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." has been delivered successfully. Thanks, come back soon.";
                $sms->sms_phone = $order["customer"]["phone"];
                $sms->sms_state = 1;
                $sms->save();

                $data = array(
                    'subject' => 'Order Item Delivered - Solushop Ghana',
                    'name' => $order["customer"]["first_name"],
                    'message' => "Your ordered item, ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." has been delivered successfully. <br><br>Thanks, come back soon."
                );

                Mail::to($order["customer"]["email"], $order["customer"]["first_name"])
                    ->queue(new Alert($data));

                /*--- Accrue to Vendor || Record Transaction ---*/
                $vendor = Vendor::where('id', $order_item['sku']["product"]["vendor"]["id"])->first();
                $vendor->balance += round(($order_item['oi_settlement_price'] - $order_item['oi_discount']) * $order_item['oi_quantity'], 2);

                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Vendor Accrual";
                $transaction->trans_amount              = round(($order_item['oi_settlement_price'] - $order_item['oi_discount']) * $order_item['oi_quantity'], 2);
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 3;
                $transaction->trans_debit_account       = $vendor->id;
                $transaction->trans_description         = $log = "Accrual of GH¢ ".round(($order_item['oi_settlement_price'] - $order_item['oi_discount']) * $order_item['oi_quantity'], 2)." to ".$vendor->name." for ordered item [ ".$order_item["id"]." ] ".$order_item["oi_quantity"]." ".$order_item["oi_name"];
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $transaction->save();


                //record transaction
                $vendor->save();

                /*--- Record Delivery History ---*/
                $delivered_item = new DeliveredItem;
                $delivered_item->di_order_item_id          = $order_item["id"];
                $delivered_item->di_marked_by_id           = Auth::guard('manager')->user()->id;
                $delivered_item->di_marked_by_description  = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $delivered_item->save();

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Item Delivered';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." marked ordered item [ ".$order_item["id"]." ] ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." as delivered.");

                /*--- Return with success message ---*/
                return redirect()->back()->with("success_message", $order_item["oi_quantity"]." ".$order_item["oi_name"]." marked as delivered successfully.");
                break;

            case 'download_delivery_guide':
                //get order items information
                $data["deliveries"] = OrderItem::orderBy('oi_name', 'asc')->whereIn('oi_state', [2, 3])->with('order.customer')->get()->toArray();

                //get customers information
                $data["customers"] =  DB::select(
                    "SELECT distinct customers.id, customers.phone, customer_addresses.ca_town, customer_addresses.ca_address, customers.first_name, customers.last_name from customers, customer_addresses, order_items, orders where (oi_state = '2' OR oi_state = '3') and order_items.oi_order_id = orders.id and orders.order_customer_id = customers.id and orders.order_address_id = customer_addresses.id order by customers.first_name"
                );

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Delivery Guide Download';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." downloaded Delivery Guide ".date('m-d-Y').".pdf");

                $pdf = PDF::loadView('portal.guides.delivery', array('data' => $data));
                return $pdf->download('Delivery Guide '.date('m-d-Y').'.pdf');
                break;
            
            default:
                return redirect()->back()->with("error_message", "Something went wrong, please try again.");
                break;
        }
    }

    public function showCoupons(){
        return view('portal.main.manager.coupons')
                ->with('coupons',  Coupon::with('state')->get()->toArray());
    }

    public function showGenerateCoupon(){
        return view('portal.main.manager.generate-coupon');
    }

    public function processGenerateCoupon(Request $request){
        /*--- Generate Coupon ---*/
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        //part one
        $coupon_id   = 'S'.
        substr(str_shuffle($permitted_chars), 7, 2).
        date('d').
        "-".
        substr(str_shuffle($permitted_chars), 7, 2).
        date('m').
        'W'.
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


        $coupon = new Coupon;
        $coupon->coupon_code = $coupon_id;
        $coupon->coupon_value = $request->value;
        $coupon->coupon_owner = "SOLUSHOP";
        $coupon->coupon_state = 2;
        $coupon->coupon_expiry_date = $request->expiry_date;
        $coupon->save();

        $count->save();

        /*--- log activity ---*/
        activity()
        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Sales Associate Registration';
        })
        ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." generated a coupon ".$coupon_id." worth GH¢".$request->value);
        
        return redirect()->back()->with("success_message", "Coupon generated successfully.");

    }

    public function showSalesAssociates(){
        return view('portal.main.manager.sales-associates')
                ->with('sales_associates',  SalesAssociate::all()->toArray());
    }

    public function showAddSalesAssociate(){
        return view('portal.main.manager.add-sales-associate');
    }

    public function processAddSalesAssociate(Request $request){
         /*--- Validate form data  ---*/
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
        $file = $identification_file;
        $file->move("/var/www/vhosts/solushop.com.gh/httpdocs/portal/s-team-member-id/", $coupon_id.".".$identification_file_ext);

        /*--- save coupon ---*/
        $coupon = new Coupon;
        $coupon->coupon_code = $coupon_id;
        $coupon->coupon_value = 0.01;
        $coupon->coupon_owner = $request->email;
        $coupon->coupon_state = 1;
        $coupon->coupon_expiry_date = "NA";
        $coupon->save();


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

        $data = array(
            'subject' => 'Sales Associate Confirmation - Solushop Ghana',
            'name' => ucwords(strtolower($request->first_name)),
            'message' => "You have been accepted as a Sales Associate on Solushop.<br><br>Email: ".$request->email."<br>Password : $passcode<br>Login here : <a href='https://www.solushop.com.gh/portal/sales-associate'> Sales Associate Portal </a>"
        );

        Mail::to($request->email, ucwords(strtolower($request->first_name)))
            ->queue(new Alert($data));


         /*--- log activity ---*/
         activity()
         ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
         ->tap(function(Activity $activity) {
             $activity->subject_type = 'System';
             $activity->subject_id = '0';
             $activity->log_name = 'Sales Associate Registration';
         })
         ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." added ".ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name))." as a sales associate");
         
         return redirect()->back()->with("success_message", ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name))." added successfully as a sales associate.");

        
    }

    public function showSalesAssociate($memberID){

        if (is_null(SalesAssociate::where('id', $memberID)->first())) {
            return redirect()->route("manager.show.sales.associates")->with("error_message", "Sales associate not found.");
        }

        $sales_associate = SalesAssociate::where('id', $memberID)->with('badge_info')->first()->toArray();
        $sales_associate["transactions"] = AccountTransaction::where([
                ['trans_credit_account_type', '=', '7'],
                ['trans_credit_account', '=', $memberID]
            ])->orWhere(
                [
                    ['trans_credit_account_type', '=', '8'],
                    ['trans_credit_account', '=', $memberID]
            ])->orWhere(
                [
                    ['trans_debit_account_type', '=', '7'],
                    ['trans_debit_account', '=', $memberID]
            ])->orWhere(
                [
                    ['trans_debit_account_type', '=', '8'],
                    ['trans_debit_account', '=', $memberID]
            ])
            ->get()
            ->toArray();


        $sales_associate["sales"] = Order::
            whereIn('order_state', [3, 4, 5, 6])
            ->where('order_scoupon', substr($sales_associate["id_file"], 0, 24))
            ->sum('order_subtotal');

        return view('portal.main.manager.view-sales-associate')
                ->with('sales_associate', $sales_associate);
    }

    public function processSalesAssociate(Request $request, $memberID){
        //select sales associates details
        $associate = SalesAssociate::where('id', $memberID)->with('badge_info')->first();
        
        switch ($request->sa_action) {
            case 'update_details':
                /*--- validate ---*/
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required|digits:10',
                    'mode_of_payment' => 'required',
                    'payment_details' => 'required',
                    'residential_address' => 'required',
                ]);
        
                if ($validator->fails()) {
                    $messageType = "error_message";
                    $messageContent = "";
        
                    foreach ($validator->messages()->getMessages() as $field_name => $messages)
                    {
                        $messageContent .= $messages[0]." "; 
                    }
        
                    return redirect()->back()->withInput(['first_name', 'last_name', 'email', 'phone', 'mode_of_payment', 'payment_details', 'residential_address'])->with($messageType, $messageContent);
                }

                //update details
                $associate->first_name = $request->first_name;
                $associate->last_name = $request->last_name;
                $associate->email = $request->email;
                $associate->phone = "233".substr($request->phone, 1);
                $associate->mode_of_payment = $request->mode_of_payment;
                $associate->payment_details = $request->payment_details;
                $associate->address = $request->residential_address;


                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Sales Associate Details Update';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." updated the details of sales associate, ".$associate->first_name." ".$associate->last_name);

                $success_message = $associate->first_name." ".$associate->last_name."'s details updated successfully.";
                $associate->save();
                
                return redirect()->back()->with("success_message", $success_message);
                break;

            case 'record_payout':
                
                /*--- Record transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Sales Associate Payout";
                $transaction->trans_amount              = $request->pay_out_amount;
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 8;
                $transaction->trans_debit_account       = $associate->id;
                $transaction->trans_description         = "Payout of GH¢ ".$request->pay_out_amount." to ".$associate->first_name." ".$associate->last_name;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $transaction->save();

                /*--- Update Associate Balance ---*/
                $associate->balance -= $request->pay_out_amount;

                /*--- Update Main Account Balance ---*/
                $counts = Count::first();
                $counts->account = round($counts->account - $request->pay_out_amount, 2);
                $counts->save();

                /*--- Notify vendor ---*/
                $sms = new SMS;
                $sms->sms_message = "Dear ".$associate->first_name.", a payout of GHS ".$request->pay_out_amount." has been recorded to you. Your new balance is GHS ".$associate->balance;
                $sms->sms_phone = $associate->phone;
                $sms->sms_state = 1;
                $sms->save();

                $data = array(
                    'subject' => 'Sales Associate Payout - Solushop Ghana',
                    'name' => $associate->first_name,
                    'message' => "A payout of GHS ".$request->pay_out_amount." has been recorded to you. Your new balance is GHS ".$associate->balance
                );

                Mail::to($associate->email, $associate->first_name)
                    ->queue(new Alert($data));

                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Sales Associate Payout';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." recorded a payout of GH¢ ".$request->pay_out_amount." to sales associate, ".$associate->first_name." ".$associate->last_name);

                $success_message = "Payout of GH¢ ".$request->pay_out_amount." to ".$associate->first_name." ".$associate->last_name." recorded successfully.";
                $associate->save();
                
                return redirect()->back()->with("success_message", $success_message);
                
                break;
            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }
    }

    public function showDeliveryPartners(){
        return view('portal.main.manager.delivery-partners')
                ->with('delivery_partners',  DeliveryPartner::all()->toArray());
    }

    public function showAddDeliveryPartner(){
        return view('portal.main.manager.add-delivery-partner');
    }

    public function processAddDeliveryPartner(Request $request){
         /*--- Validate form data  ---*/
         $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'dp_company' => 'required',
            'payment_details' => 'required'
        ]);

        if ($validator->fails()) {
            $messageType = "error_message";
            $messageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $messageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput(['first_name', 'last_name', 'email', 'phone', 'dp_company', 'payment_details'])->with($messageType, $messageContent);
        }

        //check for email existence in system
        if (DeliveryPartner::where('email', $request->email)->first()) {
            return redirect()->back()->withInput(['first_name', 'last_name', 'email', 'phone', 'dp_company', 'payment_details'])->with("error_message", "Email already associated with a Delivery Partner");
        }

        /*--- store associate data ---*/
        $delivery_partner = new DeliveryPartner;
        $delivery_partner->first_name        = ucwords(strtolower($request->first_name));
        $delivery_partner->last_name         = ucwords(strtolower($request->last_name));
        $delivery_partner->email             = $request->email;
        $delivery_partner->dp_company        = $request->dp_company;
        $delivery_partner->payment_details   = $request->payment_details;
        $delivery_partner->passcode          = $passcode = rand(1000, 9999);
        $delivery_partner->password          = bcrypt($passcode);
        $delivery_partner->payment_details   = ucwords($request->payment_details);
        $delivery_partner->balance           = 0;
        $delivery_partner->save();


         /*--- log activity ---*/
         activity()
         ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
         ->tap(function(Activity $activity) {
             $activity->subject_type = 'System';
             $activity->subject_id = '0';
             $activity->log_name = 'Delivery Partner Registration';
         })
         ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." added ".ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name))." as a delivery partner");
         
         return redirect()->back()->with("success_message", ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name))." added successfully as a delivery partner.");
    }

    public function showDeliveryPartner($partnerID){

        if (is_null(DeliveryPartner::where('id', $partnerID)->first())) {
            return redirect()->route("manager.show.delivery.partners")->with("error_message", "Delivery partner not found.");
        }

        $delivery_partner = DeliveryPartner::where('id', $partnerID)->first()->toArray();
        $delivery_partner["transactions"] = AccountTransaction::where([
                ['trans_credit_account_type', '=', '9'],
                ['trans_credit_account', '=', $partnerID]
            ])->orWhere(
                [
                    ['trans_credit_account_type', '=', '10'],
                    ['trans_credit_account', '=', $partnerID]
            ])->orWhere(
                [
                    ['trans_debit_account_type', '=', '9'],
                    ['trans_debit_account', '=', $partnerID]
            ])->orWhere(
                [
                    ['trans_debit_account_type', '=', '10'],
                    ['trans_debit_account', '=', $partnerID]
            ])
            ->get()
            ->toArray();


        return view('portal.main.manager.view-delivery-partner')
                ->with('delivery_partner', $delivery_partner);
    }

    public function processDeliveryPartner(Request $request, $partnerID){
        //select partner details
        $partner = DeliveryPartner::where('id', $partnerID)->first();
        
        switch ($request->sa_action) {
            case 'update_details':
                /*--- validate ---*/
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'dp_company' => 'required',
                    'email' => 'required|email',
                    'payment_details' => 'required'
                ]);
        
                if ($validator->fails()) {
                    $messageType = "error_message";
                    $messageContent = "";
        
                    foreach ($validator->messages()->getMessages() as $field_name => $messages)
                    {
                        $messageContent .= $messages[0]." "; 
                    }
        
                    return redirect()->back()->withInput(['first_name', 'last_name', 'email', 'dp_company', 'payment_details'])->with($messageType, $messageContent);
                }

                //update details
                $partner->first_name = $request->first_name;
                $partner->last_name = $request->last_name;
                $partner->dp_company = $request->dp_company;
                $partner->email = $request->email;
                $partner->payment_details = $request->payment_details;


                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Delivery Partner Details Update';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." updated the details of delivery partner, ".$partner->first_name." ".$partner->last_name);

                $success_message = $partner->first_name." ".$partner->last_name."'s details updated successfully.";
                $partner->save();
                
                return redirect()->back()->with("success_message", $success_message);
                break;

            case 'record_payout':
                
                /*--- Record transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Delivery Partner Payout";
                $transaction->trans_amount              = $request->pay_out_amount;
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 10;
                $transaction->trans_debit_account       = $partner->id;
                $transaction->trans_description         = "Payout of GH¢ ".$request->pay_out_amount." to ".$partner->first_name." ".$partner->last_name;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $transaction->save();

                /*--- Update Partner Balance ---*/
                $partner->balance -= $request->pay_out_amount;

                /*--- Update Main Account Balance ---*/
                $counts = Count::first();
                $counts->account = round($counts->account - $request->pay_out_amount, 2);
                $counts->save();

                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Delivery Partner Payout';
                })
                ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." recorded a payout of GH¢ ".$request->pay_out_amount." to delivery partner, ".$partner->first_name." ".$partner->last_name);

                $success_message = "Payout of GH¢ ".$request->pay_out_amount." to ".$partner->first_name." ".$partner->last_name." recorded successfully.";
                $partner->save();
                
                return redirect()->back()->with("success_message", $success_message);
                
                break;
            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }
    }

    public function showAccounts(){
        $accounts["transactions"] = AccountTransaction::all()->toArray();
        $accounts["balance"]["total"] = Count::sum('account');
        $accounts["balance"]["vendors"] = Vendor::sum('balance');
        $accounts["balance"]["sales-associates"] = SalesAssociate::sum('balance');
        $accounts["balance"]["delivery-partners"] = DeliveryPartner::sum('balance');
        $accounts["balance"]["available"] = $accounts["balance"]["total"] - $accounts["balance"]["vendors"] - $accounts["balance"]["sales-associates"];


        return view('portal.main.manager.accounts')
                ->with("accounts", $accounts);

    }

    public function processAccounts(Request $request){
        /*--- Validate form data  ---*/
        $validator = Validator::make($request->all(), [
            'payment_type' => 'required',
            'payment_amount' => 'required',
            'payment_description' => 'required'
        ]);

        if ($validator->fails()) {
            $messageType = "error_message";
            $messageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $messageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput(['payment_type', 'payment_amount', 'payment_description'])->with($messageType, $messageContent);
        } 

        $counts = Count::first();
        switch ($request->payment_type) {
            case 'Pay-Out':
                /*--- update accounts ---*/
                $counts->account = round($counts->account - $request->payment_amount, 2);
                $counts->save();

                /*--- record transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Accounts Pay Out";
                $transaction->trans_amount              = $request->payment_amount;
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 2;
                $transaction->trans_debit_account       = "EXT";
                $transaction->trans_description         = "Pay Out of GH¢ ".$request->payment_amount." - ".$request->payment_description;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $transaction->save();

                break;
            case 'Pay-In':
                /*--- update accounts ---*/
                $counts->account = round($counts->account + $request->payment_amount, 2);
                $counts->save();
                
                /*--- record transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Accounts Pay In";
                $transaction->trans_amount              = $request->payment_amount;
                $transaction->trans_credit_account_type = 2;
                $transaction->trans_credit_account      = "EXT";
                $transaction->trans_debit_account_type  = 1;
                $transaction->trans_debit_account       = "INT-SC001";
                $transaction->trans_description         = "Pay In of GH¢ ".$request->payment_amount." - ".$request->payment_description;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name;
                $transaction->save();

                break;
            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }

        /*--- log activity ---*/
        activity()
        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = "Accounts ".$request->payment_type;
        })
        ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." recorded a ".$request->payment_type." of GH¢ ".$request->payment_amount);

        /*--- Return with success message ---*/
        return redirect()->back()->with("success_message", $request->payment_type." of GH¢ ".$request->payment_amount." recorded successfully.");

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

        /*--- Notify Vendor ---*/
        $sms = new SMS;
        $sms->sms_message = "Dear ".$subscription[0]->name.", your subscription as a vendor with Solushop Ghana has been cancelled.";
        $sms->sms_phone = $subscription[0]->phone;
        $sms->sms_state = 1;
        $sms->save();

        $data = array(
            'subject' => 'Subscription Cancelled - Solushop Ghana',
            'name' => $subscription[0]->name,
            'message' => "Your subscription as a vendor with Solushop Ghana has been cancelled."
        );

        Mail::to($subscription[0]->email, $subscription[0]->name)
            ->queue(new Alert($data));

        /*--- log activity ---*/
        activity()
        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Vendor Subscription Cancellation';
        })
        ->log(Auth::guard('manager')->user()->first_name." ".Auth::guard('manager')->user()->last_name." cancelled ".$subscription[0]->name."'s subscription.");
        
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

    //guides
    public function showDeliveryGuide(){
         //get order items information
         $data["deliveries"] = OrderItem::orderBy('oi_name', 'asc')->whereIn('oi_state', [2, 3])->with('order.customer')->get()->toArray();

         //get customers information
         $data["customers"] =  DB::select(
             "SELECT distinct customers.id, customers.phone, customer_addresses.ca_town, customer_addresses.ca_address, customers.first_name, customers.last_name from customers, customer_addresses, order_items, orders where (oi_state = '2' OR oi_state = '3') and order_items.oi_order_id = orders.id and orders.order_customer_id = customers.id and orders.order_address_id = customer_addresses.id order by customers.first_name"
         );
         
        //  return view('portal.guides.delivery')
        //     ->with('data', $data);

         $pdf = PDF::loadView('portal.guides.delivery', array('data' => $data));
         return $pdf->download('Delivery Guide '.date('m-d-Y').'.pdf');
         
    }

    public function showPickUpGuide(){
        //get order items information
        $data["pick_ups"] = OrderItem::orderBy('oi_name', 'asc')->where('oi_state', 2)->with('sku.product.vendor')->get()->toArray();

        //get vendor information
        $data["vendors"] =  DB::select(
            "SELECT distinct vendors.id, vendors.phone, vendors.alt_phone, vendors.name, vendors.address from vendors, order_items, stock_keeping_units, products where oi_state = '2' and order_items.oi_sku = stock_keeping_units.id and stock_keeping_units.sku_product_id = products.id and products.product_vid = vendors.id order by vendors.name"
        );

        $pdf = PDF::loadView('portal.guides.pick-up', array('data' => $data));
        return $pdf->download('Pick-Up Guide'.date('m-d-Y').'.pdf');
    }

    public function broadcastSubscribedVendors(){
        $subscribed_vendors = DB::select(
            "SELECT *, vendor_subscriptions.id as subscription_id, vendor_subscriptions.created_at as subscription_created_at, vendor_subscriptions.updated_at as subscription_updated_at FROM vendors, vendor_subscriptions, vs_packages WHERE vendors.id = vendor_subscriptions.vs_vendor_id AND vendor_subscriptions.vs_vsp_id = vs_packages.id AND vs_days_left > 0"
        );

        for ($i=0; $i < sizeof($subscribed_vendors); $i++) { 
            $sms = new SMS;
            $sms->sms_message = "Hiya ".$subscribed_vendors[$i]->name.", it's a great time to be with Solushop. We have released a new update that introduces new and exciting features for you. Access your portal with the link and credentials below.\n\nUsername : ".$subscribed_vendors[$i]->username."\nPasscode: ".$subscribed_vendors[$i]->passcode."\nLink: https://www.solushop.com.gh/portal/vendor\n\nAccess your shop here:\nhttps://www.solushop.com.gh/shop/".$subscribed_vendors[$i]->username;
            $sms->sms_phone = $subscribed_vendors[$i]->phone;
            $sms->sms_state = 1;
            $sms->save();

            $data = array(
                'subject' => 'Notice - Solushop Ghana',
                'name' => $subscribed_vendors[0]->name,
                'message' => "It's a great time to be with Solushop. We have released a new update that introduces new and exciting features for you. Access your portal with the link and credentials below.<br><br>Username : ".$subscribed_vendors[$i]->username."<br>Passcode: ".$subscribed_vendors[$i]->passcode."<br>Link: https://www.solushop.com.gh/portal/vendor<br><br>Access your shop here:<br>https://www.solushop.com.gh/shop/".$subscribed_vendors[$i]->username
            );
    
            Mail::to($subscribed_vendors[0]->email, $subscribed_vendors[0]->name)
                ->queue(new Alert($data));
        }
    }
}
