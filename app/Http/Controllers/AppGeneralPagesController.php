<?php

namespace App\Http\Controllers;

require base_path('vendor/autoload.php');

use Slydepay\Order\Order as SlydepayOrder;
use Slydepay\Order\OrderItem as SlydepayOrderItem;
use Slydepay\Order\OrderItems as SlydepayOrderItems;
use Slydepay\Slydepay;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Validator;
use \Mobile_Detect;
use Auth;

use App\AccountTransaction;
use App\CartItem;
use App\Count;
use App\Coupon;
use App\Customer;
use App\CustomerAddress;
use App\Manager;
use App\Order;
use App\OrderItem;
use App\ProductCategory;
use App\SMS;
use App\StockKeepingUnit;
use App\Vendor;
use App\WishlistItem;
use App\WTUPackage;
use App\WTUPayment;
use App\VendorSubscription;
use App\VSPayment;

class AppGeneralPagesController extends Controller
{
   public function showCheckout(){
        if(!Auth::check()){
            return redirect()->route('login');
        }else{
            /*--- Checkout Items ---*/
            $checkout["checkout_items_id_object"] = CartItem::
                where('ci_customer_id', Auth::user()->id)
                ->with('sku')
                ->get()
                ->toArray();

            if(sizeof($checkout["checkout_items_id_object"]) < 1){
                return redirect()->route('show.shop');
            }

            $checkout['checkout_items_id_array'] = $checkout['ci_quantity'] = [];
            for ($i=0; $i < sizeof($checkout['checkout_items_id_object']); $i++) { 
                $checkout['checkout_items_id_array'][$i] = $checkout['checkout_items_id_object'][$i]['ci_sku'];
                $checkout['ci_quantity'][$i] = $checkout['checkout_items_id_object'][$i]['ci_quantity'];
            }

            //select checkout items
            $checkout['checkout_items'] = StockKeepingUnit::
                join('products', 'stock_keeping_units.sku_product_id', '=', 'products.id')
                ->join('vendors', 'products.product_vid', '=', 'vendors.id')
                ->whereIn('stock_keeping_units.id', $checkout['checkout_items_id_array'])
                ->with('product', 'product_images')
                ->orderBy('stock_keeping_units.id')
                ->get()
                ->toArray();

            /*--- Get Customer information ---*/
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //wallet balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            //customer addresses
            $customer_information['addresses'] = CustomerAddress::
                where('ca_customer_id', Auth::user()->id)
                ->get()
                ->toArray();
            

            /*--- calculating checkout totals ---*/
            //items total
            $checkout['sub_total'] = 0;
            $checkout['shipping_product'] = 0;
            for ($i=0; $i < sizeof($checkout['checkout_items']); $i++) { 

                $checkout['sub_total'] += $checkout["ci_quantity"][$i] * ($checkout['checkout_items'][$i]['product_selling_price'] - $checkout['checkout_items'][$i]['product_discount'] );

                $checkout['shipping_product'] += $checkout['checkout_items'][$i]['product_dc'];
            }

            //considering icono discount
            if(isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL){
                $checkout["icono_discount"] = 0.01 * $checkout['sub_total'];
                $checkout['sub_total'] = 0.99 * $checkout['sub_total'];
            }

            
            //calculating shipping
            if(isset(Auth::user()->default_address) and strtolower((Auth::user()->default_address)) != "none"){
                $checkout_sf_object = CustomerAddress::
                    where([
                        ['ca_customer_id',"=", Auth::user()->id],
                        ['id',"=", Auth::user()->default_address]
                    ])
                    ->with('shipping_fare')
                    ->first()
                    ->toArray();

                $checkout["shipping_base"] = $checkout_sf_object['shipping_fare']['sf_fare'];
            }else{
                $checkout["shipping_base"] = 15;
            }

            //adding shipping to subtotal
            $checkout['shipping'] = $checkout['shipping_product'] + $checkout['shipping_base'];
            $checkout["sub_total"] += $checkout['shipping'];

            /*--- calculating total due (from wallet and payable) ---*/
            if($customer_information['wallet_balance'] > 0){
                if ($customer_information['wallet_balance'] >= $checkout['sub_total']) {
                    $checkout['due_from_wallet'] = $checkout['sub_total'];
                    $checkout['total_due'] = 0;
                }else{
                    $checkout['due_from_wallet'] = $customer_information['wallet_balance'];
                    $checkout['total_due'] = $checkout['sub_total'] - $checkout['due_from_wallet'];
                }
            }else{
                $checkout['total_due'] = $checkout['sub_total'];
            }

            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
                return view('mobile.main.general.checkout')
                ->with('customer_information', $customer_information)
                ->with('checkout', $checkout);
            }else{
                /*---selecting search bar categories (level 2 categories)---*/
                $search_bar_pc_options = ProductCategory::
                where('pc_level', 2) 
                ->orderBy('pc_description')   
                ->get(['id', 'pc_description', 'pc_slug']);
                return view('app.main.general.checkout')
                        ->with('search_bar_pc_options', $search_bar_pc_options)
                        ->with('customer_information', $customer_information)
                        ->with('checkout', $checkout);
            }
        }
   }

   public function processCheckout(Request $request){
        switch ($request->checkout_action) {
           
            case 'update_personal_details':
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'phone' => 'required|digits:10',
                    'email' => 'required|email'
                ]);

                if ($validator->fails()) {
                    $errorMessageType = "error_message";
                    $errorMessageContent = "";

                    foreach ($validator->messages()->getMessages() as $field_name => $messages)
                    {
                        $errorMessageContent .= $messages[0]." "; 
                    }

                    return redirect()->back()->withInput($request->only('email', 'phone', 'first_name', 'last_name'))->with($errorMessageType, $errorMessageContent);
                }

                $customer = Customer::
                    where("id", Auth::user()->id)
                    ->first();

                $customer->first_name = $request->first_name;
                $customer->last_name = $request->last_name;
                $customer->email = $request->email;
                $customer->phone = "233".substr($request->phone, 1);

                $customer->save();

                /*--- log activity ---*/
                activity()
                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Personal Details Updated';
                })
                ->log(Auth::user()->email.' updated their personal details.');

                return redirect()->back()->with("success_message", "Details updated successfully.");

                break;

            case 'update_default_address':
                $customer = Customer::
                where("id", Auth::user()->id)
                ->first();

                $customer->default_address = $request->default_address;

                $customer->save();

                /*--- log activity ---*/
                activity()
                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Default Address updated.';
                })
                ->log(Auth::user()->email.' updated their default address.');

                return redirect()->back()->with("success_message", "Address updated successfully.");
                break;

            case 'remove_checkout_item':
                CartItem::where([
                        ['ci_sku', "=", $request->checkout_item_sku],
                        ['ci_customer_id', "=", Auth::user()->id]
                ])
                ->delete();

                /*--- log activity ---*/
                activity()
                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Checkout Item Removed';
                })
                ->log(Auth::user()->email.' removed item '.$request->checkout_item_sku.' at checkout.');
                return redirect()->back()->with("success_message", "Item removed successfully.");
                break;

            case 'process_checkout':
                if(!Auth::check()){
                    return redirect()->route('login');
                }else{
                    /*--- Checkout Items ---*/
                    $checkout["checkout_items_id_object"] = CartItem::
                        where('ci_customer_id', Auth::user()->id)
                        ->with('sku')
                        ->get()
                        ->toArray();
        
                    if(sizeof($checkout["checkout_items_id_object"]) < 1){
                        return redirect()->route('show.shop');
                    }

                    if(strtolower(Auth::user()->default_address) == "none"){
                        return redirect()->back()->with("error_message", "Kindly add an address.");
                    }
        
                    $checkout['checkout_items_id_array'] = $checkout['ci_quantity'] = [];
                    for ($i=0; $i < sizeof($checkout['checkout_items_id_object']); $i++) { 
                        $checkout['checkout_items_id_array'][$i] = $checkout['checkout_items_id_object'][$i]['ci_sku'];
                        $checkout['ci_quantity'][$i] = $checkout['checkout_items_id_object'][$i]['ci_quantity'];
                    }
        
                    //select checkout items
                    $checkout['checkout_items'] = StockKeepingUnit::
                        join('products', 'stock_keeping_units.sku_product_id', '=', 'products.id')
                        ->join('vendors', 'products.product_vid', '=', 'vendors.id')
                        ->whereIn('stock_keeping_units.id', $checkout['checkout_items_id_array'])
                        ->with('product', 'product_images')
                        ->orderBy('stock_keeping_units.id')
                        ->get()
                        ->toArray();
        
                    /*--- Get Customer information ---*/
                    $customer_information_object = Customer::
                        where('id', Auth::user()->id)
                        ->with('milk', 'chocolate', 'cart', 'wishlist')
                        ->first()
                        ->toArray();
        
                    //wallet balance
                    $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);
        
                    //cart count
                    $customer_information['cart_count'] = sizeof($customer_information_object['cart']);
        
                    //wishlist count
                    $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

                    //unread messages
                    $customer_id = Auth::user()->id;
                    $unread_messages = DB::select(
                        "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
                    );

                    $customer_information['unread_messages'] = $unread_messages[0]->unread;
        
                    //customer addresses
                    $customer_information['addresses'] = CustomerAddress::
                        where('ca_customer_id', Auth::user()->id)
                        ->get()
                        ->toArray();
                    
        
                    /*--- calculating checkout totals ---*/
                    //items total
                    $checkout['sub_total'] = $checkout['order_total'] = 0;
                    $checkout['shipping_product'] = 0;
                    for ($i=0; $i < sizeof($checkout['checkout_items']); $i++) { 
        
                        $checkout['sub_total'] += $checkout["ci_quantity"][$i] * ($checkout['checkout_items'][$i]['product_selling_price'] - $checkout['checkout_items'][$i]['product_discount'] );
        
                        $checkout['shipping_product'] += $checkout['checkout_items'][$i]['product_dc'];
                    }

                    $checkout['order_total'] = $checkout['sub_total'];
                    $order_item_total = $checkout['sub_total'];
        
                    //considering icono discount
                    if(isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL){
                        $checkout["icono_discount"] = 0.01 * $checkout['sub_total'];
                        $checkout['sub_total'] = 0.99 * $checkout['sub_total'];
                    }

                    
        
                    
                    //calculating shipping
                    if(isset(Auth::user()->default_address) and !is_null(Auth::user()->default_address)){
                        $checkout_sf_object = CustomerAddress::
                            where([
                                ['ca_customer_id',"=", Auth::user()->id],
                                ['id',"=", Auth::user()->default_address]
                            ])
                            ->with('shipping_fare')
                            ->first()
                            ->toArray();
        
                        $checkout["shipping_base"] = $checkout_sf_object['shipping_fare']['sf_fare'];
                    }else{
                        $checkout["shipping_base"] = 15;
                    }
        
                    //adding shipping to subtotal
                    $checkout['shipping'] = $checkout['shipping_product'] + $checkout['shipping_base'];
                    $checkout["sub_total"] += $checkout['shipping'];
        
                    /*--- calculating total due (from wallet and payable) ---*/
                    if($customer_information['wallet_balance'] > 0){
                        if ($customer_information['wallet_balance'] >= $checkout['sub_total']) {
                            $checkout['due_from_wallet'] = $checkout['sub_total'];
                            $checkout['total_due'] = 0;
                        }else{
                            $checkout['due_from_wallet'] = $customer_information['wallet_balance'];
                            $checkout['total_due'] = $checkout['sub_total'] - $checkout['due_from_wallet'];
                        }
                    }else{
                        $checkout['total_due'] = $checkout['sub_total'];
                    }

                    /*--- generate order internally ---*/
                    //generating order id eg OD010720190191
                    $count = Count::first();
                    $count->order_count++;
                    $order_id = "OD".date('Ymd').substr("0000".$count->order_count, strlen(strval($count->order_count)));

                    //insert order
                    $order = new Order;
                    $order->id = $order_id;
                    $order->order_type = 0;
                    $order->order_customer_id = Auth::user()->id;
                    $order->order_address_id = Auth::user()->default_address;
                    $order->order_subtotal = $checkout['order_total'];
                    $order->order_shipping = $checkout['shipping'];

                    if(trim($request->order_ad) != ""){
                        $order->order_ad = ucfirst($request->order_ad);
                    }else{
                        $order->order_ad = NULL;
                    }

                    $order->order_token = NULL;
                    
                    if(isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL){
                        $order->order_scoupon = Auth::user()->icono;
                    }else{
                        $order->order_scoupon = NULL;
                    }

                    $order->order_state = 1;
                    $order->order_date = date('Y-m-d H:i:s');

                    $order->save();
                    $count->save();

                    //inserting order items
                    for ($i=0; $i < sizeof($checkout['checkout_items']); $i++) {
                        $order_item = new OrderItem;
                        $order_item->oi_order_id            = $order_id;
                        $order_item->oi_sku                 = $checkout['checkout_items_id_array'][$i];
                        $order_item->oi_name                = $checkout["checkout_items"][$i]["product_name"];
                        if (trim(strtolower($checkout["checkout_items"][$i]["sku_variant_description"] )) != "none") {
                            $order_item->oi_name .= " - ".$checkout["checkout_items"][$i]["sku_variant_description"];
                        }
                        $checkout["checkout_items"][$i]["product_name"] = $order_item->oi_name;
                        $order_item->oi_selling_price       = $checkout["checkout_items"][$i]["product_selling_price"];
                        $order_item->oi_settlement_price    = $checkout["checkout_items"][$i]["product_settlement_price"];
                        $order_item->oi_discount            = $checkout["checkout_items"][$i]["product_discount"];
                        $order_item->oi_quantity            = $checkout['ci_quantity'][$i];
                        $order_item->oi_state               = 1;
                        $order_item->save();
                    }

                    //deleting cart items
                    CartItem::
                    where("ci_customer_id", Auth::user()->id)
                    ->delete();

                    /*--- log activity ---*/
                    activity()
                    ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                    ->tap(function(Activity $activity) {
                        $activity->subject_type = 'System';
                        $activity->subject_id = '0';
                        $activity->log_name = 'Order Checkout';
                    })
                    ->log(Auth::user()->email.' checked out. [ '.$order_id.' ]');


                    if ($checkout['total_due'] > 0) {
                        /*--- generate order externally (Slydepay) ---*/
                        $slydepay = new Slydepay("ceo@solutekworld.com", "1466854163614");

                        //build array of local order items
                        $order_items_local = [];
                        for ($i=0; $i < sizeof($checkout['checkout_items']); $i++) {
                            $order_items_local[$i] = new SlydepayOrderItem($checkout['checkout_items_id_array'][$i], $checkout["checkout_items"][$i]["product_name"], ($checkout["checkout_items"][$i]["product_selling_price"] - $checkout["checkout_items"][$i]["product_discount"]), $checkout['ci_quantity'][$i]);
                        }

                        if ($customer_information['wallet_balance'] > 0) {
                            $order_items_local[$i] = new SlydepayOrderItem("S-WBD", "Deducted from Solushop Wallet", ($customer_information['wallet_balance'] - (2 * $customer_information['wallet_balance'])), 1);
                            $i++;
                        }

                        if(isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL){
                            $order_items_local[$i] = new SlydepayOrderItem("S-SCD", "S-Coupon Discount - ".Auth::user()->icono, (0.01 * $order_item_total - (2 * 0.01 * $order_item_total)), 1);
                        }

                        $order_items = new SlydepayOrderItems($order_items_local);

                        $shipping_cost = $checkout['shipping']; 
                        $tax = 0;

                        $order_tid = rand(100000, 999999);

                        // Create the Order object for this transaction. 
                        $slydepay_order = SlydepayOrder::createWithId(
                            $order_items,
                            $order_tid, 
                            $shipping_cost,
                            $tax,
                            "Payment to Solushop Ghana for Order $order_id",
                            "No comment"
                        );

                        try{
                            $response = $slydepay->processPaymentOrder($slydepay_order);
                            $redirect_url = $response->redirectUrl();
                            $redirect_url_break = explode("=", $redirect_url);

                            Order::
                            where('id', $order_id)
                            ->update([
                                'order_token' => $redirect_url_break[1],
                                'order_tid'   => $order_tid
                            ]);

                            return redirect($redirect_url);
                        } catch (Slydepay\Exception\ProcessPayment $e) {
                            echo $e->getMessage();
                        }
                    }else{
                        /*--- process payment internally ---*/
                        //reduce account balance
                        $customer = Customer::
                            where('id', Auth::user()->id)
                            ->with('chocolate', 'milk')
                            ->first();

                        $newCustomerBalance     = round((($customer->milk->milk_value * $customer->milkshake) - $customer->chocolate->chocolate_value) - $checkout['sub_total'], 2);
                        $newCustomerMilkshake   = ($newCustomerBalance + $customer->chocolate->chocolate_value) / $customer->milk->milk_value;
                        $customer->milkshake    = $newCustomerMilkshake;

                        //remove icono
                        $customer->icono = NULL;
                        $customer->save();

                        /*--- Record transaction ---*/
                        $transaction = new AccountTransaction;
                        $transaction->trans_type                = "Order Payment (S-Wallet)";
                        $transaction->trans_amount              = round($checkout["sub_total"], 2);
                        $transaction->trans_credit_account_type = 5;
                        $transaction->trans_credit_account      = Auth::user()->id;
                        $transaction->trans_debit_account_type  = 1;
                        $transaction->trans_debit_account       = "INT-SC001";
                        $transaction->trans_description         = "Payment of GH¢ ".round($checkout["sub_total"], 2)." for order ".$order_id;
                        $transaction->trans_date                = date("Y-m-d G:i:s");
                        $transaction->trans_recorder            = "System";
                        $transaction->save();

                        //update order
                        Order::
                            where('id', $order_id)
                            ->update([
                                'order_state' => 2
                            ]);

                        /*--- Check for first time order ---*/
                        if ($order_id == Order::orderby('order_date', 'asc')->whereIn('order_state', [2, 3, 4, 5, 6])->where('order_customer_id', Auth::user()->id)->first()->id) {
                            //record five cedis bonus
                            /*--- Record transaction ---*/
                            $transaction = new AccountTransaction;
                            $transaction->trans_type                = "Sign up bonus for ".Auth::user()->email;
                            $transaction->trans_amount              = 5;
                            $transaction->trans_credit_account_type = 1;
                            $transaction->trans_credit_account      = "INT-SC001";
                            $transaction->trans_debit_account_type  = 6;
                            $transaction->trans_debit_account       = Auth::user()->id;
                            $transaction->trans_description         = "Payout of GH¢ 5 for first time order ".$order_id." of customer ".Auth::user()->email;
                            $transaction->trans_date                = date("Y-m-d G:i:s");
                            $transaction->trans_recorder            = "System";
                            $transaction->save();
                        }
                        
                        //update order items quantity
                        for ($i=0; $i < sizeof($checkout['checkout_items']); $i++) {
                            $sku = StockKeepingUnit::
                                where('id', $checkout['checkout_items_id_array'][$i])
                                ->first();

                            //reduce quantity
                            $sku->sku_stock_left -= $checkout['ci_quantity'][$i];

                            /*--- Notify Vendor ---*/
                            $sms = new SMS;
                            $sms->sms_message = "Purchase Alert\nProduct : " .$checkout["checkout_items"][$i]["product_name"]. "\nQuantity Bought: " . $checkout['ci_quantity'][$i] . "\nQuantity Remaining : " .$sku->sku_stock_left;
                            $sms->sms_phone = $checkout["checkout_items"][$i]["phone"];
                            $sms->sms_state = 1;
                            $sms->save();

                            //save sku
                            $sku->save();
                        }
                        

                        //notify customer
                        $sms_message = "Hi ".Auth::user()->first_name.", your order $order_id has been received. We will begin processing soon. Thanks for choosing Solushop!";
                        $sms_phone = Auth::user()->phone;

                        $sms = new SMS;
                        $sms->sms_message = $sms_message;
                        $sms->sms_phone = $sms_phone;
                        $sms->sms_state = 1;
                        $sms->save();

                        //notify management
                        $managers = Manager::where('sms', 0)->get();
                        foreach ($managers as $manager) {
                            $sms = new SMS;
                            $sms->sms_message = "ALERT: NEW ORDER\nCustomer: ".Auth::user()->first_name." ".Auth::user()->last_name."\nPhone: 0".substr(Auth::user()->phone, 3);
                            $sms->sms_phone = $manager->phone;
                            $sms->sms_state = 1;
                            $sms->save();
                        }

                        return redirect()->route('show.account.orders')->with("success_message", "Order $order_id placed successfully.");
                    }
                }

                
                break;
            default:
                # code...
                break;
        }
   }
    
    public function showCart(){
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            $cart['logged_in'] = 0;

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            //get cart items
            $cart['cart_items_id_object'] = CartItem::
                where('ci_customer_id', Auth::user()->id)
                ->orderBy('ci_sku')
                ->get()
                ->toArray();

            //build cart items array for select
            $cart['cart_items_id_array'] = $cart['ci_quantity'] = [];
            for ($i=0; $i < sizeof($cart['cart_items_id_object']); $i++) { 
                $cart['cart_items_id_array'][$i] = $cart['cart_items_id_object'][$i]['ci_sku'];
                $cart['ci_quantity'][$i] = $cart['cart_items_id_object'][$i]['ci_quantity'];
            }

            if (sizeof($cart['cart_items_id_array']) < 1) {
                $cart['cart_items'] = [];
                $detect = new Mobile_Detect;

                if( $detect->isMobile() && !$detect->isTablet() ){
                    return view('mobile.main.general.cart')
                    ->with('customer_information', $customer_information)
                    ->with('cart', $cart);
                }else{
                    /*---selecting search bar categories (level 2 categories)---*/
                    $search_bar_pc_options = ProductCategory::
                    where('pc_level', 2) 
                    ->orderBy('pc_description')   
                    ->get(['id', 'pc_description', 'pc_slug']);

                    return view('app.main.general.cart')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information)
                    ->with('cart', $cart);
                }
            }else{
                //select cart items
                $cart['cart_items'] = StockKeepingUnit::
                    join('products', 'stock_keeping_units.sku_product_id', '=', 'products.id')
                    ->join('vendors', 'products.product_vid', '=', 'vendors.id')
                    ->whereIn('stock_keeping_units.id', $cart['cart_items_id_array'])
                    ->with('product', 'product_images')
                    ->orderBy('stock_keeping_units.id')
                    ->get()
                    ->toArray();

                //calculating cart totals
                $cart['sub_total'] = 0;
                for ($i=0; $i < sizeof($cart['cart_items']); $i++) { 
                    $cart['sub_total'] += $cart["ci_quantity"][$i] * ($cart['cart_items'][$i]['product_selling_price'] - $cart['cart_items'][$i]['product_discount'] );
                }

                //considering icono discount
                if(isset(Auth::user()->icono) AND Auth::user()->icono != "NULL" AND Auth::user()->icono != NULL){
                    $cart["icono_discount"] = 0.01 * $cart['sub_total'];
                    $cart['sub_total'] = 0.99 * $cart['sub_total'];
                }



                $detect = new Mobile_Detect;
                if( $detect->isMobile() && !$detect->isTablet() ){
                    return view('mobile.main.general.cart')
                    ->with('customer_information', $customer_information)
                    ->with('cart', $cart);
                }else{
                    /*---selecting search bar categories (level 2 categories)---*/
                    $search_bar_pc_options = ProductCategory::
                    where('pc_level', 2) 
                    ->orderBy('pc_description')   
                    ->get(['id', 'pc_description', 'pc_slug']);

                    return view('app.main.general.cart')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information)
                    ->with('cart', $cart);
                }
                
            }


        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;

            $cart['logged_in'] = 1;

            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
                return view('mobile.main.general.cart')
                ->with('customer_information', $customer_information)
                ->with('cart', $cart);
            }else{
                /*---selecting search bar categories (level 2 categories)---*/
                $search_bar_pc_options = ProductCategory::
                where('pc_level', 2) 
                ->orderBy('pc_description')   
                ->get(['id', 'pc_description', 'pc_slug']);
                return view('app.main.general.cart')
                        ->with('search_bar_pc_options', $search_bar_pc_options)
                        ->with('customer_information', $customer_information)
                        ->with('cart', $cart);
            }
        }
    }

    public function processCartAction(Request $request){
        switch ($request->cart_action) {
            case 'remove_icono':
                $customer = Customer::
                where('id', Auth::user()->id)
                ->first();

                $customer->icono = NULL;
                $customer->save();

                /*--- log activity ---*/
                activity()
                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Sales Coupon Removed';
                })
                ->log(Auth::user()->email.' removed sales coupon.');

                return redirect()->back()->with('success_message', 'Coupon removed successfully.');
                break;
            case 'remove_cart_item':
                $cart_item = CartItem::
                where([
                        ['ci_sku', "=", $request->cart_item_sku],
                        ['ci_customer_id', "=", Auth::user()->id]
                ])
                ->delete();

                /*--- log activity ---*/
                activity()
                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Cart Item Removed';
                })
                ->log(Auth::user()->email.' removed item '.$request->cart_item_sku.' from cart.');

                return redirect()->back()->with('success_message', 'Item removed successfully.');
                break;
            case 'update_cart':
                for ($i=0; $i < $request->cart_count; $i++) { 
                    if(!is_numeric($request->input('quantity'.$i))){
                        $cart_quantity = 1;
                    }else{
                        $cart_quantity = $request->input('quantity'.$i);
                    }

                    DB::table('cart_items')
                    ->where([
                        ['ci_sku', "=", $request->input('sku'.$i)],
                        ['ci_customer_id', "=", Auth::user()->id],
                    ])
                    ->update([
                        'ci_quantity' => $cart_quantity
                    ]);
                }
                /*--- log activity ---*/
                activity()
                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Cart Updated';
                })
                ->log(Auth::user()->email.' updated their cart.');

                return redirect()->back()->with('success_message', 'Cart updated successfully.');
                break;
            case 'apply_coupon':
                if (trim($request->coupon_code) == "") {
                    return redirect()->back()->with('error_message', 'Please enter a valid coupon.');
                }else{
                    //check validity of coupon
                    $coupon = Coupon::
                    where('coupon_code', $request->coupon_code)
                    ->first();

                    if($coupon){
                        if(substr($coupon->coupon_code, 10, 1) == 'S'){
                            //add coupon
                            $customer = Customer::
                            where('id', Auth::user()->id)
                            ->first();

                            $customer->icono = $coupon->coupon_code;
                            $customer->save();

                            /*--- log activity ---*/
                            activity()
                            ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                            ->tap(function(Activity $activity) {
                                $activity->subject_type = 'System';
                                $activity->subject_id = '0';
                                $activity->log_name = 'Sales Coupon Applied';
                            })
                            ->log(Auth::user()->email.' applied sales coupon '.$coupon->coupon_code);


                            return redirect()->back()->with('success_message', 'Coupon applied successfully.');
                        }elseif(substr($coupon->coupon_code, 10, 1) == 'W'){
                            //check if coupon is not used
                            if ($coupon->coupon_state == 3) {
                                //coupon is used
                                /*--- log activity ---*/
                                activity()
                                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                                ->tap(function(Activity $activity) {
                                    $activity->subject_type = 'System';
                                    $activity->subject_id = '0';
                                    $activity->log_name = 'Wallet Coupon Redeem Attempt';
                                })
                                ->log(Auth::user()->email.' attempted to redeem already used coupon '.$coupon->coupon_code);

                                return redirect()->back()->with('error_message', 'Coupon already redeemed.');
                            }
                            elseif ($coupon->coupon_state == 4) {
                                //coupon is expired
                                /*--- log activity ---*/
                                activity()
                                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                                ->tap(function(Activity $activity) {
                                    $activity->subject_type = 'System';
                                    $activity->subject_id = '0';
                                    $activity->log_name = 'Wallet Coupon Redeem Attempt';
                                })
                                ->log(Auth::user()->email.' attempted to redeem expired coupon '.$coupon->coupon_code);

                                return redirect()->back()->with('error_message', 'Coupon expired.');
                            }elseif($coupon->coupon_state == 2){
                                //update customer balance
                                $customer = Customer::
                                where('id', Auth::user()->id)
                                ->with('chocolate', 'milk')
                                ->first();

                                $newCustomerBalance     = round((($customer->milk->milk_value * $customer->milkshake) - $customer->chocolate->chocolate_value) + $coupon->coupon_value, 2);
                                $newCustomerMilkshake   = ($newCustomerBalance + $customer->chocolate->chocolate_value) / $customer->milk->milk_value;
                                $customer->milkshake    = $newCustomerMilkshake;
                                $customer->save();

                                //reduce account balance
                                $count = Count::first();
                                $count->account = round($count->account - $coupon->coupon_value, 2);
                                $count->save();

                                //record transaction
                                

                                //update coupon state
                                $coupon_code = $coupon->coupon_code;
                                $coupon_value = $coupon->coupon_value;
                                $coupon->coupon_state = 3;
                                $coupon->save();

                                //notify customer
                                //queue customer message
                                $sms_message = "Hi ".ucwords(strtolower(Auth::user()->first_name)).", you have successfully redeemed a coupon worth GHS $coupon_value. Your account balance is now GHS $newCustomerBalance";
                                $sms_phone = Auth::user()->phone;

                                $sms = new SMS;
                                $sms->sms_message = $sms_message;
                                $sms->sms_phone = $sms_phone;
                                $sms->sms_state = 1;
                                $sms->save();

                                /*--- log activity ---*/
                                activity()
                                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                                ->tap(function(Activity $activity) {
                                    $activity->subject_type = 'System';
                                    $activity->subject_id = '0';
                                    $activity->log_name = 'Wallet Coupon Redeemed';
                                })
                                ->log(Auth::user()->email.' redeemed wallet coupon '.$coupon_code);
                                return redirect()->back()->with('success_message', 'Coupon redeemed successfully.');
                            }else{
                                return redirect()->back()->with('error_message', 'Please enter a valid coupon.');
                            }
                        }
                    }else{
                        return redirect()->back()->with('error_message', 'Please enter a valid coupon.');
                    }
                }
                break;
            
            default:
                return redirect()->back()->with('error_message', 'Something went wrong. Try again please.');
                break;
        }
    }
    
    public function showWishlist(){
        

        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            //get wishlist items
            if ($customer_information['wishlist_count'] < 1) {
                $detect = new Mobile_Detect;
                if( $detect->isMobile() && !$detect->isTablet() ){
                    return view('mobile.main.general.wishlist')
                    ->with('customer_information', $customer_information);
                }else{
                    /*---selecting search bar categories (level 2 categories)---*/
                    $search_bar_pc_options = ProductCategory::
                    where('pc_level', 2) 
                    ->orderBy('pc_description')   
                    ->get(['id', 'pc_description', 'pc_slug']);
                    return view('app.main.general.wishlist')
                        ->with('search_bar_pc_options', $search_bar_pc_options)
                        ->with('customer_information', $customer_information);
                }

            }else{
                $wishlist['wishlist_items'] = WishlistItem::
                    where('wi_customer_id', Auth::user()->id)
                    ->with('product', 'product_images')
                    ->get()
                    ->toArray();

                for ($i=0; $i < sizeof($wishlist['wishlist_items']); $i++) { 
                    $availability_check = StockKeepingUnit::
                        where([
                            ['sku_product_id', "=", $wishlist['wishlist_items'][$i]['product']['id']],
                            ['sku_stock_left', ">", 0]
                        ])
                        ->get();

                    if (sizeof($availability_check) < 1) {
                        $wishlist['wishlist_items'][$i]['availability'] = 1;
                    }else{
                        $wishlist['wishlist_items'][$i]['availability'] = 0;
                    }

                    

                    $wishlist['wishlist_items'][$i]["vendor"] = Vendor::
                    where('id', $wishlist['wishlist_items'][$i]["product"]["product_vid"])
                    ->get("username")
                    ->first()
                    ->toArray();
                }

                $detect = new Mobile_Detect;
                if( $detect->isMobile() && !$detect->isTablet() ){
                    return view('mobile.main.general.wishlist')
                    ->with('customer_information', $customer_information)
                    ->with('wishlist', $wishlist);
                }else{
                    /*---selecting search bar categories (level 2 categories)---*/
                    $search_bar_pc_options = ProductCategory::
                    where('pc_level', 2) 
                    ->orderBy('pc_description')   
                    ->get(['id', 'pc_description', 'pc_slug']);
                    return view('app.main.general.wishlist')
                        ->with('search_bar_pc_options', $search_bar_pc_options)
                        ->with('customer_information', $customer_information)
                        ->with('wishlist', $wishlist);
                }
            }

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;


            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
                return view('mobile.main.general.wishlist')
                ->with('customer_information', $customer_information);
            }else{
                /*---selecting search bar categories (level 2 categories)---*/
                $search_bar_pc_options = ProductCategory::
                where('pc_level', 2) 
                ->orderBy('pc_description')   
                ->get(['id', 'pc_description', 'pc_slug']);
                return view('app.main.general.wishlist')
                        ->with('search_bar_pc_options', $search_bar_pc_options)
                        ->with('customer_information', $customer_information);
            }

        }
    }

    public function processWishlistAction(Request $request){
        if($request->wishlist_action = "remove_wishlist_item"){
            WishlistItem::
                where([
                    ['wi_customer_id', '=', Auth::user()->id],
                    ['wi_product_id', '=', $request->wishlist_item_id]
                ])
            ->delete();
            /*--- log activity ---*/
            activity()
            ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
            ->tap(function(Activity $activity) {
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Wishlist Item Removed';
            })
            ->log(Auth::user()->email.' removed item '.$request->wishlist_item_id.' from wishlist.');

            return redirect()->back()->with('success_message', 'Item removed successfully.');
        }else{
            return redirect()->back()->with('error_message', 'Something went wrong, please try again');
        }
    }
    
    public function showAbout(){
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.about')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.about')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        }

        
    }

    public function showContact(){
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.contact')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.contact')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        }
    }

    public function showTNC(){
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.terms-and-conditions')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.terms-and-conditions')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        }
    }

    public function showPrivacyPolicy(){
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.privacy-policy')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.privacy-policy')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        }
    }

    public function showReturnPolicy(){
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.return-policy')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.return-policy')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        }
    }

    public function showFAQ(){
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.faq')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.faq')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        };
    }

    public function show404(){
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            //unread messages
            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );

            $customer_information['unread_messages'] = $unread_messages[0]->unread;

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.404')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.404')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        };
    }

    public function processReceiveCallback(){
        /*--- Get call back variables ---*/
        $status_code         = filter_input(INPUT_GET, "status", FILTER_SANITIZE_STRING);
        $transaction_id      = filter_input(INPUT_GET, "transac_id", FILTER_SANITIZE_STRING);
        $order_reference      = filter_input(INPUT_GET, "cust_ref", FILTER_SANITIZE_STRING);
        $payment_token       = filter_input(INPUT_GET, "pay_token", FILTER_SANITIZE_STRING);

        
        /*--- Check for payment type ---*/
        $payment_type = "Not Set";
        if(Order::where('order_token', $payment_token)->first()){

            //order payment
            $payment_type = "order";

        }elseif(WTUPayment::where('wtu_payment_token', $payment_token)->first()){

            //wallet top up payment
            $payment_type = "wallet_top_up";

        }elseif(VSPayment::where('vs_payment_token', $payment_token)->first()){

            //subscription payment
            $payment_type = "subscription";

        }



        echo $payment_type;

        switch ($payment_type) {
            case 'order':
                /*--- Check for payment status and redirect appropriately ---*/
                if($status_code != 0){
                    return redirect()->route("show.account.orders")->with("error_message", "Order payment unsuccessful.");
                }

                /*--- process order ---*/
                $order = Order::where('order_token', $payment_token)->first();

                /*--- Order Items ---*/
                $order->checkout_items = OrderItem::
                where('oi_order_id', $order->id)
                ->get()
                ->toArray();

                /*--- Order Total ---*/
                $order_shipping = $order->order_shipping;
                
                //considering icono discount
                if($order->order_scoupon != NULL AND trim($order->order_scoupon) != "NULL"){
                    $order_subtotal = round(0.99 * $order->order_subtotal, 2);
                }else{
                    $order_subtotal = $order->order_subtotal;
                }

                $order_total = $order_subtotal + $order_shipping;
                

                /*--- check for customer account balance ---*/
                $customer_information_object = Customer::
                    where('id', Auth::user()->id)
                    ->with('milk', 'chocolate')
                    ->first()
                    ->toArray();
    
                //calculate account balance
                $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

                if($customer_information['wallet_balance'] > 0){
                    /*--- deduct part of the money from balance ---*/
                     /*--- increase account balance ---*/
                     $count = Count::first();
                     $count->account += round(0.975 * ($order_total - $customer_information['wallet_balance']), 2);
                     $count->save();
 
                     /*--- Record slydepay charge transaction ---*/
                     $transaction = new AccountTransaction;
                     $transaction->trans_type                = "Slydepay Charge, Order ID - ".$order->id;
                     $transaction->trans_amount              = round(0.025 * ($order_total - $customer_information['wallet_balance']), 2);
                     $transaction->trans_credit_account_type = 1;
                     $transaction->trans_credit_account      = "INT-SC001";
                     $transaction->trans_debit_account_type  = 2;
                     $transaction->trans_debit_account       = "EXT";
                     $transaction->trans_description         = "Slydepay Charge of GH¢ ".round(0.025 * ($order_total - $customer_information['wallet_balance']), 2)." for order payment ".$order->id;
                     $transaction->trans_date                = date("Y-m-d G:i:s");
                     $transaction->trans_recorder            = "System";
                     $transaction->save();
 
                     /*--- Record main transaction ---*/
                     $transaction = new AccountTransaction;
                     $transaction->trans_type                = "Part Order Payment (Slydepay), Order ID - ".$order->id;
                     $transaction->trans_amount              = ($order_total - $customer_information['wallet_balance']);
                     $transaction->trans_credit_account_type = 6;
                     $transaction->trans_credit_account      = Auth::guard()->user()->id;
                     $transaction->trans_debit_account_type  = 1;
                     $transaction->trans_debit_account       = "INT-SC001";
                     $transaction->trans_description         = "Payment of GH¢ ".(($order_total - $customer_information['wallet_balance']))." received for order with ID ".$order->id;
                     $transaction->trans_date                = date("Y-m-d G:i:s");
                     $transaction->trans_recorder            = "System";
                     $transaction->save();

                     /*--- Reduce account balance to 0 and record transaction ---*/

                    $transaction = new AccountTransaction;
                    $transaction->trans_type                = "Part Order Payment (S-Wallet), Order ID - ".$order->id;
                    $transaction->trans_amount              = round($customer_information['wallet_balance'], 2);
                    $transaction->trans_credit_account_type = 5;
                    $transaction->trans_credit_account      = Auth::guard()->user()->id;
                    $transaction->trans_debit_account_type  = 1;
                    $transaction->trans_debit_account       = "INT-SC001";
                    $transaction->trans_description         = "Payment of GH¢ ".round($customer_information['wallet_balance'], 2)." received for order with ID ".$order->id;
                    $transaction->trans_date                = date("Y-m-d G:i:s");
                    $transaction->trans_recorder            = "System";
                    $transaction->save();

                    $customer = Customer::where('id', Auth::user()->id)->first();
                    $newCustomerBalance     = 0;
                    $newCustomerMilkshake   = ($newCustomerBalance + $customer->chocolate->chocolate_value) / $customer->milk->milk_value;
                    $customer->milkshake    = $newCustomerMilkshake;
                    $customer->save();

                    //update order
                    Order::
                    where('id', $order->id)
                    ->update([
                        'order_state' => 2
                    ]);

                    /*--- Check first time order ---*/
                    if ($order->id == Order::orderby('order_date', 'asc')->whereIn('order_state', [2, 3, 4, 5, 6])->where('order_customer_id', Auth::user()->id)->first()->id) {
                        //record five cedis bonus
                        $count = Count::first();
                        $count->account -= 5;
                        $count->save();

                        /*--- Record transaction ---*/
                        $transaction = new AccountTransaction;
                        $transaction->trans_type                = "Sign up bonus for ".Auth::user()->email;
                        $transaction->trans_amount              = 5;
                        $transaction->trans_credit_account_type = 1;
                        $transaction->trans_credit_account      = "INT-SC001";
                        $transaction->trans_debit_account_type  = 6;
                        $transaction->trans_debit_account       = Auth::user()->id;
                        $transaction->trans_description         = "Payout of GH¢ 5 for first time order ".$checkout['order']["id"]." of customer ".Auth::user()->email;
                        $transaction->trans_date                = date("Y-m-d G:i:s");
                        $transaction->trans_recorder            = "System";
                        $transaction->save();
                    }

                }else{
                    /*--- deduct all of the money from balance ---*/

                    /*--- increase account balance ---*/
                    $count = Count::first();
                    $count->account += (round(0.975 * $order_total, 2));
                    $count->save();

                    /*--- Record slydepay charge transaction ---*/
                    $transaction = new AccountTransaction;
                    $transaction->trans_type                = "Slydepay Charge, Order ID - ".$order->id;
                    $transaction->trans_amount              = round(0.025 * $order_total, 2);
                    $transaction->trans_credit_account_type = 1;
                    $transaction->trans_credit_account      = "INT-SC001";
                    $transaction->trans_debit_account_type  = 2;
                    $transaction->trans_debit_account       = "EXT";
                    $transaction->trans_description         = "Slydepay Charge of GH¢ ".round(0.025 * $order_total, 2)." for order payment ".$order->id;
                    $transaction->trans_date                = date("Y-m-d G:i:s");
                    $transaction->trans_recorder            = "System";
                    $transaction->save();

                    /*--- Record main transaction ---*/
                    $transaction = new AccountTransaction;
                    $transaction->trans_type                = "Order Payment WTUPID - ".$order->id;
                    $transaction->trans_amount              = $order_total;
                    $transaction->trans_credit_account_type = 6;
                    $transaction->trans_credit_account      = Auth::guard()->user()->id;
                    $transaction->trans_debit_account_type  = 1;
                    $transaction->trans_debit_account       = "INT-SC001";
                    $transaction->trans_description         = "Payment of GH¢ ".($order_total)." received for order with ID ".$order->id;
                    $transaction->trans_date                = date("Y-m-d G:i:s");
                    $transaction->trans_recorder            = "System";
                    $transaction->save();
                    
                     //update order
                        Order::
                        where('id', $order->id)
                        ->update([
                            'order_state' => 2
                        ]);

                    /*--- Check first time order ---*/
                    if ($order->id == Order::orderby('order_date', 'asc')->whereIn('order_state', [2, 3, 4, 5, 6])->where('order_customer_id', Auth::user()->id)->first()->id) {
                        //record five cedis bonus
                        $count = Count::first();
                        $count->account -= 5;
                        $count->save();

                        /*--- Record transaction ---*/
                        $transaction = new AccountTransaction;
                        $transaction->trans_type                = "Sign up bonus for ".Auth::user()->email;
                        $transaction->trans_amount              = 5;
                        $transaction->trans_credit_account_type = 1;
                        $transaction->trans_credit_account      = "INT-SC001";
                        $transaction->trans_debit_account_type  = 6;
                        $transaction->trans_debit_account       = Auth::user()->id;
                        $transaction->trans_description         = "Payout of GH¢ 5 for first time order ".$checkout['order']["id"]." of customer ".Auth::user()->email;
                        $transaction->trans_date                = date("Y-m-d G:i:s");
                        $transaction->trans_recorder            = "System";
                        $transaction->save();
                    }
                }

                //update order items quantity
                for ($i=0; $i < sizeof($order->checkout_items); $i++) {
                    $sku = StockKeepingUnit::
                        where('id', $order->checkout_items[$i]["oi_sku"])
                        ->first();

                    //reduce quantity
                    $sku->sku_stock_left -= $order->checkout_items[$i]["oi_quantity"];

                    /*--- Notify Vendor ---*/
                    $vendor =  DB::select(
                        "SELECT phone FROM vendors, products, stock_keeping_units WHERE products.product_vid = vendors.id AND stock_keeping_units.sku_product_id = products.id AND stock_keeping_units.id = '".$order->checkout_items[$i]["oi_sku"]."'"
                    );

                    
                    $sms = new SMS;
                    $sms->sms_message = "Purchase Alert\nProduct : " .$order->checkout_items[$i]["oi_name"]. "\nQuantity Bought: " . $order->checkout_items[$i]["oi_quantity"] . "\nQuantity Remaining : " .$sku->sku_stock_left;
                    $sms->sms_phone = $vendor[0]->phone;
                    $sms->sms_state = 1;
                    $sms->save();

                    //save sku
                    $sku->save();
                }
                

                //notify customer
                $sms_message = "Hi ".Auth::user()->first_name.", your order ".$order->id." has been received. We will begin processing soon. Thanks for choosing Solushop!";
                $sms_phone = Auth::user()->phone;

                $sms = new SMS;
                $sms->sms_message = $sms_message;
                $sms->sms_phone = $sms_phone;
                $sms->sms_state = 1;
                $sms->save();

                //notify management
                $managers = Manager::where('sms', 0)->get();
                foreach ($managers as $manager) {
                    $sms = new SMS;
                    $sms->sms_message = "ALERT: NEW ORDER\nCustomer: ".Auth::user()->first_name." ".Auth::user()->last_name."\nPhone: 0".substr(Auth::user()->phone, 3);
                    $sms->sms_phone = $manager->phone;
                    $sms->sms_state = 1;
                    $sms->save();
                }

                /*--- log activity ---*/
                activity()
                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Received';
                })
                ->log(Auth::user()->email.' placed order. [ '.$order->id.' ]');

                /*--- Redirect with success message ---*/
                return redirect()->route("show.account.orders")->with("success_message", "Order payment successful.");

                break;

            case 'wallet_top_up':
                /*--- Check for payment status and redirect appropriately ---*/
                if($status_code != 0){
                    return redirect()->route("show.account.wallet")->with("error_message", "Top-Up payment unsuccessful.");
                }

                /*--- process top up ---*/
                $wtu_payment = WTUPayment::where('wtu_payment_token', $payment_token)->first();

                $wtu_package = WTUPackage::where('id', $wtu_payment->wtu_payment_wtup_id)->first();

                /*--- increase account balance ---*/
                $count = Count::first();
                $count->account += (round(0.975 * $wtu_package->wtu_package_cost, 2));
                $count->save();

                /*--- Record slydepay charge transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Slydepay Charge, WTUPID - ".$wtu_payment->id;
                $transaction->trans_amount              = round(0.025 * $wtu_package->wtu_package_cost, 2);
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 2;
                $transaction->trans_debit_account       = "EXT";
                $transaction->trans_description         = "Slydepay Charge of GH¢ ".round(0.025 * $wtu_package->wtu_package_cost, 2)." for wallet top up payment ".$wtu_payment->id;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = "System";
                $transaction->save();

                /*--- Record main transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Wallet Top Up Payment WTUPID - ".$wtu_payment->id;
                $transaction->trans_amount              = $wtu_package->wtu_package_cost;
                $transaction->trans_credit_account_type = 6;
                $transaction->trans_credit_account      = Auth::guard()->user()->id;
                $transaction->trans_debit_account_type  = 1;
                $transaction->trans_debit_account       = "INT-SC001";
                $transaction->trans_description         = "Payment of GH¢ ".($wtu_package->wtu_package_cost)." received for wallet top up with ID ".$wtu_payment->id;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = "System";
                $transaction->save();

                /*--- Record wallet debit transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Wallet Top Up Payment WTUPID - ".$wtu_payment->id;
                $transaction->trans_amount              = round(1.02 * $wtu_package->wtu_package_cost);
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 5;
                $transaction->trans_debit_account       = Auth::guard()->user()->id;
                $transaction->trans_description         = "Wallet top up of GH¢ ".round(1.02 * $wtu_package->wtu_package_cost)." for wallet top up with ID ".$wtu_payment->id;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = "System";
                $transaction->save();

                /*--- Record wallet bonus transaction ---*/
                $count = Count::first();
                $count->account -= (round($wtu_package->wtu_package_bonus, 2));
                $count->save();

                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Wallet Top Up Bonus Payment WTUPID - ".$wtu_payment->id;
                $transaction->trans_amount              = $wtu_package->wtu_package_bonus;
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 6;
                $transaction->trans_debit_account       = Auth::guard()->user()->id;
                $transaction->trans_description         = "Wallet top bonus of GH¢ ".($wtu_package->wtu_package_bonus)." for wallet top up with ID ".$wtu_payment->id;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = "System";
                $transaction->save();


                /*--- Update customer account balance ---*/
                $customer = Customer::
                    where('id', Auth::user()->id)
                    ->with('chocolate', 'milk')
                    ->first();

                $newCustomerBalance     = round((($customer->milk->milk_value * $customer->milkshake) - $customer->chocolate->chocolate_value) + $wtu_package->wtu_package_cost, 2);
                $newCustomerMilkshake   = ($newCustomerBalance + $customer->chocolate->chocolate_value) / $customer->milk->milk_value;
                $customer->milkshake    = $newCustomerMilkshake;
                $customer->save();
                
                /*--- Log activity ---*/
                activity()
                ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Wallet Top Up';
                })
                ->log(Auth::user()->first_name." ".Auth::user()->last_name." topped up their S-Wallet with GH¢ ".($wtu_package->wtu_package_cost));

                /*--- Confirm payment on slydepay ---*/
                // $slydepay = new Slydepay("ceo@solutekworld.com", "1466854163614");
                // $slydepay->confirmTransaction($payment_token, $transaction_id);
                
                /*--- Redirect with success message ---*/
                return redirect()->route("show.account.wallet")->with('success_message', 'Wallet top up of GH¢ '.($wtu_package->wtu_package_cost).' successful.');
                

                break;

            case 'subscription':
                /*--- Check for payment status and redirect appropriately ---*/
                if($status_code != 0){
                    return redirect()->route("vendor.show.subscription")->with("error_message", "Subscription payment unsuccessful.");
                }

                /*--- process subscription ---*/
                $vs_payment = VSPayment::where('vs_payment_token', $payment_token)->first();

                /*--- increase account balance ---*/
                $count = Count::first();
                $count->account += (round(0.975 * $vs_payment->vs_payment_amount, 2));
                $count->save();

                /*--- Record slydepay charge transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Slydepay Charge, VSPID - ".$vs_payment->id;
                $transaction->trans_amount              = round(0.025 * $vs_payment->vs_payment_amount, 2);
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 2;
                $transaction->trans_debit_account       = "EXT";
                $transaction->trans_description         = "Slydepay Charge of GH¢ ".round(0.025 * $vs_payment->vs_payment_amount, 2)." for subscription payment ".$vs_payment->id;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = "System";
                $transaction->save();

                /*--- Record main transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Vendor Subscription Payment VSPID - ".$vs_payment->id;
                $transaction->trans_amount              = $vs_payment->vs_payment_amount;
                $transaction->trans_credit_account_type = 4;
                $transaction->trans_credit_account      = Auth::guard('vendor')->user()->id;
                $transaction->trans_debit_account_type  = 1;
                $transaction->trans_debit_account       = "INT-SC001";
                $transaction->trans_description         = "Payment of GH¢ ".($vs_payment->vs_payment_amount)." for subscription ".$vs_payment->vs_payment_vsp_id;
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = "System";
                $transaction->save();


                if(is_null(VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->first())){
                    //subscribe
                    $vendor_subscription = New VendorSubscription;
                    $vendor_subscription->vs_vendor_id = Auth::guard('vendor')->user()->id;
                    $vendor_subscription->vs_vsp_id = $vs_payment->vs_payment_vsp_id;
                    $vendor_subscription->vs_days_left = $vs_payment->vs_payment_vspq * 30;
                    $vendor_subscription->save();
    
                    /*--- Log activity ---*/
                    activity()
                    ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                    ->tap(function(Activity $activity) {
                        $activity->subject_type = 'System';
                        $activity->subject_id = '0';
                        $activity->log_name = 'Subscription Change';
                    })
                    ->log(Auth::guard('vendor')->user()->name." subscribed to package ".$vendor_subscription->vs_vsp_id." for ".($vs_payment->vs_payment_vspq * 30)." days");
    
                    /*--- Confirm payment on slydepay ---*/
                    // $slydepay = new Slydepay("ceo@solutekworld.com", "1466854163614");
                    // $slydepay->confirmTransaction($payment_token, $transaction_id);
                    
                    return redirect()->route("vendor.show.subscription")->with('success_message', 'Subscription successful and is valid for '.($vs_payment->vs_payment_vspq * 30).' days');
    
                }else{
                    if(VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->first()->vs_vsp_id == $vs_payment->vs_payment_vsp_id){
                        //extend
                        $vendor_subscription = VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->first();
                        $vendor_subscription->vs_days_left = $vendor_subscription->vs_days_left + ($vs_payment->vs_payment_vspq * 30);
    
                        /*--- Log activity ---*/
                        activity()
                        ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                        ->tap(function(Activity $activity) {
                            $activity->subject_type = 'System';
                            $activity->subject_id = '0';
                            $activity->log_name = 'Subscription Extension';
                        })
                        ->log(Auth::guard('vendor')->user()->name." extended subscription to ".$vendor_subscription->vs_days_left." days");
    
                        $vendor_subscription->save();

                        /*--- Confirm payment on slydepay ---*/
                        // $slydepay = new Slydepay("ceo@solutekworld.com", "1466854163614");
                        // $slydepay->confirmTransaction($payment_token, $transaction_id);

                        return redirect()->route("vendor.show.subscription")->with('success_message', 'Subscription extended by '.($vs_payment->vs_payment_vspq * 30).' days');
                    }else{
                        //update
                        $vendor_subscription = VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->first();
                        $vendor_subscription->vs_vsp_id = $vs_payment->vs_payment_vsp_id;
                        $vendor_subscription->vs_days_left = $vs_payment->vs_payment_vspq * 30;
    
                        /*--- Log activity ---*/
                        activity()
                        ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                        ->tap(function(Activity $activity) {
                            $activity->subject_type = 'System';
                            $activity->subject_id = '0';
                            $activity->log_name = 'Subscription Change';
                        })
                        ->log(Auth::guard('vendor')->user()->name." changed subscription to package ".$vs_payment->vs_payment_vsp_id." for ".($vs_payment->vs_payment_vspq * 30)." days");
    
                        $vendor_subscription->save();

                        /*--- Confirm payment on slydepay ---*/
                        // $slydepay = new Slydepay("ceo@solutekworld.com", "1466854163614");
                        // $slydepay->confirmTransaction($payment_token, $transaction_id);

                        return redirect()->route("vendor.show.subscription")->with('success_message', 'Subscription successful and is valid for '.($vs_payment->vs_payment_vspq * 30).' days');
                    }
                }


                break;
            
            default:
                //Do absolutely nothing.
                break;
        }
    }
}
