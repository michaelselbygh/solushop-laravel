<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Validator;
use Auth;

use App\AccountTransaction;
use App\ActivityLog;
use App\Count;
use App\Coupon;
use App\Customer;
use App\DeliveredItem;
use App\DeliveryPartner;
use App\Manager;
use App\Order;
use App\OrderItem;
use App\PickedUpItem;
use App\Product;
use App\SABadge;
use App\SalesAssociate;
use App\SMS;
use App\StockKeepingUnit;
use App\Vendor;
use App\VendorSubscription;



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
        ->log(Auth::guard('manager')->user()->email." updated the details of customer, ".$request->first_name." ".$request->last_name);

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

                    //promote where necessary
                    if ($ots < 20000 && $nts >= 20000) {
                        //promote to elite
                        $sales_associate->badge = 4;

                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name.", you are now an Elite Sales Associate. You can now enjoy 4% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();
                    }elseif($ots < 5000 && $nts >= 5000){
                        //promote to veteran
                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name.", you are now an Veteran Sales Associate. You can now enjoy 3% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();
                        $sales_associate->badge = 3;
                    }elseif($ots == 0 && $nts > 0){
                        //promote to rookie
                        $sales_associate->badge = 2;
                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name." on your first sale. You are now a Rookie sales associate. Keep selling to become a Veteran and enjoy 3% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();
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

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Payment Receipt Confirmation';
                })
                ->log(Auth::guard('manager')->user()->email." confirmed payment receipt for order ".$orderID);
                
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

                    //promote where necessary
                    if ($ots < 20000 && $nts >= 20000) {
                        //promote to elite
                        $sales_associate->badge = 4;

                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name.", you are now an Elite Sales Associate. You can now enjoy 4% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();
                    }elseif($ots < 5000 && $nts >= 5000){
                        //promote to veteran
                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name.", you are now an Veteran Sales Associate. You can now enjoy 3% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();
                        $sales_associate->badge = 3;
                    }elseif($ots == 0 && $nts > 0){
                        //promote to rookie
                        $sales_associate->badge = 2;
                        $sms = new SMS;
                        $sms->sms_message = "Congrats ".$sales_associate->first_name." on your first sale. You are now a Rookie sales associate. Keep selling to become a Veteran and enjoy 3% commission on all orders.";
                        $sms->sms_phone = $sales_associate->phone;
                        $sms->sms_state = 1;
                        $sms->save();
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

                /*--- Log Activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Payment Receipt Confirmation';
                })
                ->log(Auth::guard('manager')->user()->email." confirmed payment receipt for order ".$orderID);
                
                return redirect()->back()->with("success_message", "Order payment receipt confirmed.");
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
                ->log(Auth::guard('manager')->user()->email." cancelled order $orderID.");
                
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
                $sms_message="Hi ".$customer->first_name.", your order $orderID has been cancelled. A refund of GHS ";
                if (isset($order->order_scoupon) AND $order->order_scoupon != NULL AND $order->order_scoupon != "NULL") {
                    $sms_message .= (0.99 * $order->order_subtotal);
                } else {
                    $sms_message .= $order->order_subtotal;
                }
                $sms_message .= " has been done to your Solushop Wallet. We apologize for any inconvenience caused.";
                $sms = new SMS;
                $sms->sms_message = $sms_message;
                $sms->sms_phone = $customer->phone;
                $sms->sms_state = 1;
                $sms->save();

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
                ->log(Auth::guard('manager')->user()->email." cancelled order $orderID.");

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
                $sms->sms_message = $sms_message;
                $sms->sms_phone = $customer->phone;
                $sms->sms_state = 1;
                $sms->save();

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
                ->log(Auth::guard('manager')->user()->email." cancelled order $orderID.");

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
                ->log(Auth::guard('manager')->user()->email." recorded an ".$log);
                
                return redirect()->back()->with("success_message", "Shipping charge recorded successfully.");

                break;
            
            default:
                # code...
                break;
        }
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
        return view('portal.main.manager.pick-up-history')
                ->with('picked_up_items',  PickedUpItem::with('order_item')->get()->toArray());
    }

    public function showActivePickups(){
        return view('portal.main.manager.pick-ups')
                ->with('pick_up_items',  OrderItem::whereIn('oi_state', [2, 3])->with("sku.product.images")->get()->toArray());
    }

    public function processActivePickups(Request $request){
        echo $request->picked_up_item_id;
        exit;
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
        echo $request->delivered_item_id;
        exit;
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
        ->log(Auth::guard('manager')->user()->email." generated a coupon ".$coupon_id." worth GH¢".$request->value);
        
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
        $sub_path = 'portal/s-team-member-id/'; 
        $destination_path = public_path($sub_path);  
        $identification_file->move($destination_path,  $coupon_id.".".$identification_file_ext);

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


         /*--- log activity ---*/
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
                ->log(Auth::guard('manager')->user()->email." updated the details of sales associate, ".$associate->first_name." ".$associate->last_name);

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

                /*--- log activity ---*/
                activity()
                ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Sales Associate Payout';
                })
                ->log(Auth::guard('manager')->user()->email." recorded a payout of GH¢ ".$request->pay_out_amount." to sales associate, ".$associate->first_name." ".$associate->last_name);

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
         ->log(Auth::guard('manager')->user()->email." added ".ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name))." as a delivery partner");
         
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
                ->log(Auth::guard('manager')->user()->email." updated the details of delivery, ".$partner->first_name." ".$partner->last_name);

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
                ->log(Auth::guard('manager')->user()->email." recorded a payout of GH¢ ".$request->pay_out_amount." to delivery partner, ".$partner->first_name." ".$partner->last_name);

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
                $transaction->trans_description         = "Pay Out of GH¢ ".$request->payment_amount;
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
                $transaction->trans_description         = "Pay In of GH¢ ".$request->payment_amount;
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
            $activity->log_name = 'Sales Associate Payout';
        })
        ->log(Auth::guard('manager')->user()->email." recorded a ".$request->payment_type." of GH¢ ".$request->payment_amount);

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

        /*--- log activity ---*/
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
