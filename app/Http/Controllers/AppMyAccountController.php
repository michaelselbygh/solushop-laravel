<?php

namespace App\Http\Controllers;

use Slydepay\Order\Order as SlydepayOrder;
use Slydepay\Order\OrderItem as SlydepayOrderItem;
use Slydepay\Order\OrderItems as SlydepayOrderItems;
use Slydepay\Slydepay;

use Illuminate\Http\Request;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use \Mobile_Detect;
use Auth;
use Mail;

use App\Mail\Alert;

use App\AccountTransaction;
use App\CartItem;
use App\Conversation;
use App\Coupon;
use App\Customer;
use App\CustomerAddress;
use App\Manager;
use App\Message;
use App\MessageFlag;
use App\Order;
use App\OrderItem;
use App\Product;
use App\ProductCategory;
use App\ShippingFare;
use App\SMS;
use App\StockKeepingUnit;
use App\Vendor;
use App\WishlistItem;
use App\WTUPackage;
use App\WTUPayment;

class AppMyAccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showDashboard()
    {
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
            return view('mobile.main.general.my-account.dashboard')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.dashboard')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        };
    }

    public function showMessages(){
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

            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );
            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            /*--- messages ---*/
            //Get all messages with pagination
            $conversations = Conversation::where([
                ['conv_key', 'LIKE', '%'.Auth::user()->id.'%']
            ])
            ->get()
            ->toArray();


            for ($i=0; $i < sizeof($conversations); $i++) { 
                $conversation_key = explode("|", $conversations[$i]['conv_key']);
                $vendor = Vendor::
                where([
                    ['id', "=", trim($conversation_key[1])]
                ])->first()->toArray();

                $conversations[$i]['vendor'] = $vendor;

                //getting unread messages
                $customer_id = Auth::user()->id;
                $unread_messages = DB::select(
                    "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key = '".$conversations[$i]['conv_key']."'"
                );
                $conversations[$i]['unread_messages'] = $unread_messages[0]->unread;
            }


        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.my-account.messages')
            ->with('customer_information', $customer_information)
            ->with('conversations', $conversations);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.messages')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information)
                    ->with('conversations', $conversations);
        };
    }

    public function showConversation($vendorSlug, $productSlug = null){
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

            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );
            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            /*--- Get vendor ---*/
           
            //check
            if (is_null(Vendor::
                where([
                    ['username', "=", $vendorSlug]
                ])->first())) {
                return redirect()->route('show.account.messages')->with("error_message", "Vendor not found.");
            }

            $conversation["vendor"] = Vendor::
            where([
                ['username', "=", $vendorSlug]
            ])->first()->toArray();
            
            /*--- Get Conversation ---*/
            if (is_null( Conversation::where([
                ['conv_key', 'LIKE', '%'.Auth::user()->id.'%'],
                ['conv_key', 'LIKE', '%'.$conversation["vendor"]["id"].'%']
            ])
            ->first())) {
                //create conversation
                $new_conversation = new Conversation;
                $new_conversation->conv_key = Auth::user()->id."|".$conversation["vendor"]["id"];
                $new_conversation->save();
            }

            $conversation["details"] = Conversation::where([
                ['conv_key', 'LIKE', '%'.Auth::user()->id.'%'],
                ['conv_key', 'LIKE', '%'.$conversation["vendor"]["id"].'%']
            ])
            ->first()
            ->toArray();
            
            


            $conversation["messages"] = Message::where([
                ['message_conversation_id', '=', $conversation["details"]["id"]]
            ])
            ->get()
            ->toArray();

            /*--- get product if it is set ---*/
            if(isset($productSlug)){
                $conversation["product"] = Product::
                where('product_slug', $productSlug)
                ->first()
                ->toArray();
            }

            //update read
            Message::
            where([
                ['message_conversation_id', "=", $conversation["details"]["id"]],
                ['message_sender', "<>", Auth::user()->id],
                ['message_read', "NOT LIKE", "%".Auth::user()->id."%"]
            ])
            ->update([
                'message_read' => DB::raw('CONCAT(message_read, "'.'|'.Auth::user()->id.'")')
            ]);


        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.my-account.conversation')
            ->with('customer_information', $customer_information)
            ->with('conversation', $conversation);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.conversation')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information)
                    ->with('conversation', $conversation);
        };
    }
    
    public function processConversation(Request $request){
        
        $message = new Message;
        $message->message_sender = Auth::user()->id;
        $message->message_content = $request->message_content;
        $message->message_conversation_id = substr($request->mci, 6);
        $message->message_timestamp = date("Y-m-d H:i:s");
        $message->message_read = "Init|".Auth::user()->id;
        $message->save();

        /*--- flag message where necessary ---*/
        $flag_keywords =  ['call', 'meet', 'talk', 'whatsapp', 'facebook', 'instagram', 'phone', 'ring', 'message', 'reduce', 'reduction', 'discount', 'twitter', 'email'];
        if (is_numeric($request->message_content)){
            $flag = 1;
        }

        for ($i=0; $i < sizeof($flag_keywords); $i++) { 
            if (strpos(strtolower($request->message_content), $flag_keywords[$i]) !== false) {
                $flag = 1;
                break;
            }
        }

        if(isset($flag)){
            //insert flag
            $message_flag = new MessageFlag;
            $message_flag->mf_mid = $message->id;
            $message_flag->save();

        /*--- log activity ---*/
        activity()
        ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Message Flagged!';
        })
        ->log("Flag raised on message sent by ".Auth::user()->email.' ['.$request->message_content.']');
        }
        /*--- log activity ---*/
        activity()
        ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Message Sent';
        })
        ->log(Auth::user()->email.' sent a message ['.$request->message_content.']');

        return redirect()->back()->with("success_message", "Message sent successfully.");
    }

    public function showPersonalDetails(){
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
            return view('mobile.main.general.my-account.personal-details')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.personal-details')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        };
    }

    public function processPersonalDetails(Request $request){
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

        $customer->first_name = ucwords(strtolower($request->first_name));
        $customer->last_name = ucwords(strtolower($request->last_name));
        $customer->email = strtolower($request->email);
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
    }

    public function showOrders(){
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

            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );
            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            //select orders
            $orders = Order::orderBy('order_date', 'desc')
                ->where('order_customer_id', Auth::user()->id)
                ->with('order_state', 'order_items.order_item_state')
                ->paginate(6);

            
        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.my-account.orders')
            ->with('customer_information', $customer_information)
            ->with('orders', $orders);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.orders')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information)
                    ->with('orders', $orders);
        };
    }


    public function processOrders(Request $request){
        if(!Auth::check()){
            return redirect()->route('login');
        }else{
            /*--- Order Details ---*/
            $checkout["order"] = Order::
                where('id', $request->oid)
                ->first()
                ->toArray();


            /*--- Checkout Items ---*/
            $checkout["checkout_items"] = OrderItem::
                where('oi_order_id', $request->oid)
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
            

            /*--- calculating checkout totals ---*/
            $order_item_total = $checkout["order"]["order_subtotal"];

            //considering icono discount
            if($checkout["order"]["order_scoupon"] != NULL AND trim($checkout["order"]["order_scoupon"]) != "NULL"){
                $checkout["order"]["order_subtotal"] = 0.99 * $checkout["order"]["order_subtotal"];
            }

            $checkout["order"]["total"] = $checkout["order"]["order_subtotal"] + $checkout["order"]["order_shipping"];

            /*--- log activity ---*/
            activity()
            ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
            ->tap(function(Activity $activity) {
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Order Checkout';
            })
            ->log(Auth::user()->email.' checked out. [ '.$checkout["order"]["id"].' ]');

            /*--- calculating total due (from wallet and payable) ---*/
            if($customer_information['wallet_balance'] > 0){
                if ($customer_information['wallet_balance'] >= $checkout["order"]["total"]) {
                    $checkout['due_from_wallet'] = $checkout["order"]["total"];
                    $checkout['total_due'] = 0;
                }else{
                    $checkout['due_from_wallet'] = $customer_information['wallet_balance'];
                    $checkout['total_due'] = $checkout["order"]["total"] - $checkout['due_from_wallet'];
                }
            }else{
                $checkout['total_due'] = $checkout["order"]["total"];
            }


            if ($checkout['total_due'] > 0) {
                /*--- generate order externally (Slydepay) ---*/
                $slydepay = new Slydepay("ceo@solutekworld.com", "1466854163614");

                //build array of local order items
                $order_items_local = [];
                for ($i=0; $i < sizeof($checkout['checkout_items']); $i++) {
                    $order_items_local[$i] = new SlydepayOrderItem($checkout['checkout_items'][$i]["oi_sku"], $checkout["checkout_items"][$i]["oi_name"], ($checkout["checkout_items"][$i]["oi_selling_price"] - $checkout["checkout_items"][$i]["oi_discount"]), $checkout["checkout_items"][$i]["oi_quantity"]);
                }

                if ($customer_information['wallet_balance'] > 0) {
                    $order_items_local[$i] = new SlydepayOrderItem("S-WBD", "Deducted from Solushop Wallet", ($customer_information['wallet_balance'] - (2 * $customer_information['wallet_balance'])), 1);
                    $i++;
                }

                if($checkout["order"]["order_scoupon"] != NULL AND trim($checkout["order"]["order_scoupon"]) != "NULL"){
                    $order_items_local[$i] = new SlydepayOrderItem("S-SCD", "S-Coupon Discount - ".$checkout["order"]["order_scoupon"], (0.01 * $order_item_total - (2 * 0.01 * $order_item_total)), 1);
                }

                $order_items = new SlydepayOrderItems($order_items_local);

                $shipping_cost = $checkout['order']["order_shipping"]; 
                $tax = 0;

                $order_tid = rand(100000, 999999);

                // Create the Order object for this transaction. 
                $slydepay_order = SlydepayOrder::createWithId(
                    $order_items,
                    $order_tid, 
                    $shipping_cost,
                    $tax,
                    "Payment to Solushop Ghana for Order ".$checkout['order']["id"],
                    "No comment"
                );

                try{
                    $response = $slydepay->processPaymentOrder($slydepay_order);
                    $redirect_url = $response->redirectUrl();
                    $redirect_url_break = explode("=", $redirect_url);

                    Order::
                    where('id', $checkout['order']["id"])
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

                $newCustomerBalance     = round((($customer->milk->milk_value * $customer->milkshake) - $customer->chocolate->chocolate_value) - $checkout['order']["total"], 2);
                $newCustomerMilkshake   = ($newCustomerBalance + $customer->chocolate->chocolate_value) / $customer->milk->milk_value;
                $customer->milkshake    = $newCustomerMilkshake;

                //remove icono
                $customer->icono = NULL;
                $customer->save();

                /*--- Record transaction ---*/
                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Order Payment (S-Wallet)";
                $transaction->trans_amount              = round($checkout['order']["total"], 2);
                $transaction->trans_credit_account_type = 5;
                $transaction->trans_credit_account      = Auth::user()->id;
                $transaction->trans_debit_account_type  = 1;
                $transaction->trans_debit_account       = "INT-SC001";
                $transaction->trans_description         = "Payment of GH¢ ".round($checkout['order']["total"], 2)." for order ".$checkout['order']["id"];
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = "System";
                $transaction->save();

                /*--- log activity ---*/
                activity()
                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Received';
                })
                ->log(Auth::user()->email.' placed order. [ '.$checkout["order"]["id"].' ]');


                //update order
                Order::
                    where('id', $checkout['order']["id"])
                    ->update([
                        'order_state' => 2
                    ]);

                /*--- Check first time order ---*/
                if ($checkout['order']["id"] == Order::orderby('order_date', 'asc')->whereIn('order_state', [2, 3, 4, 5, 6])->where('order_customer_id', Auth::user()->id)->first()->id) {
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
                
                //update order items quantity
                for ($i=0; $i < sizeof($checkout['checkout_items']); $i++) {
                    $sku = StockKeepingUnit::
                        where('id', $checkout['checkout_items'][$i]["oi_sku"])
                        ->first();

                    //reduce quantity
                    $sku->sku_stock_left -= $checkout["checkout_items"][$i]["oi_quantity"];

                    /*--- Notify Vendor ---*/
                    $vendor =  DB::select(
                        "SELECT phone, email, name FROM vendors, products, stock_keeping_units WHERE products.product_vid = vendors.id AND stock_keeping_units.sku_product_id = products.id AND stock_keeping_units.id = '".$checkout['checkout_items'][$i]["oi_sku"]."'"
                    );

                    
                    $sms = new SMS;
                    $sms->sms_message = "Purchase Alert\nProduct : " .$checkout["checkout_items"][$i]["oi_name"]. "\nQuantity Bought: " . $checkout["checkout_items"][$i]["oi_quantity"] . "\nQuantity Remaining : " .$sku->sku_stock_left;
                    $sms->sms_phone = $vendor[0]->phone;
                    $sms->sms_state = 1;
                    $sms->save();

                    $data = array(
                        'subject' => 'Purchase Alert - Solushop Ghana',
                        'name' => $vendor[0]->name,
                        'message' => "You have a new order.<br><br>Product : " .$checkout["checkout_items"][$i]["oi_name"]. "<br>Quantity Bought: " . $checkout["checkout_items"][$i]["oi_quantity"] . "<br>Quantity Remaining : " .$sku->sku_stock_left."<br>"
                    );

                    Mail::to($vendor[0]->email, $vendor[0]->name)
                        ->queue(new Alert($data));

                    //save sku
                    $sku->save();
                }
                

                //notify customer
                $sms_message = "Hi ".Auth::user()->first_name.", your order ".$checkout['order']["id"]." has been received. We will begin processing soon. Thanks for choosing Solushop!";
                $sms_phone = Auth::user()->phone;

                $sms = new SMS;
                $sms->sms_message = $sms_message;
                $sms->sms_phone = $sms_phone;
                $sms->sms_state = 1;
                $sms->save();

                $data = array(
                    'subject' => 'Order Received - Solushop Ghana',
                    'name' => Auth::user()->first_name,
                    'message' => "Your order ".$checkout['order']["id"]." has been received. We will begin processing soon. Thanks for choosing Solushop!"
                );

                Mail::to(Auth::user()->email, Auth::user()->first_name)
                    ->queue(new Alert($data));

                //notify management
                $managers = Manager::get();
                foreach ($managers as $manager) {

                    $data = array(
                        'subject' => 'New Order - Solushop Ghana',
                        'name' => $manager->first_name,
                        'message' => "This email is to inform you that a new order ".$checkout['order']["id"]." has been received. If you are not required to take any action during order processing, please treat this email as purely informational.<br><br>Customer: ".Auth::user()->first_name." ".Auth::user()->last_name."<br>Phone: 0".substr(Auth::user()->phone, 3)
                    );

                    Mail::to($manager->email, $manager->first_name)
                        ->queue(new Alert($data));
                }

                return redirect()->back()->with("success_message", "Order ".$checkout['order']["id"]." placed successfully.");
            }
        }
    }

    public function showLoginAndSecurity(){
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
            return view('mobile.main.general.my-account.login-and-security')
            ->with('customer_information', $customer_information);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.login-and-security')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information);
        };
    }

    public function processLoginAndSecurity(Request $request){
        if (Hash::check($request->current_password, Auth::user()->password) == false)
        {
            return redirect()->back()->with("error_message", "Invalid current password.");  
        }

        if ($request->new_password != $request->confirm_new_password)
        {
            return redirect()->back()->with("error_message", "New passwords must match.");  
        }

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        /*--- log activity ---*/
        activity()
        ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Password Updated';
        })
        ->log(Auth::user()->email.' updated their password.');

        return redirect()->back()->with("success_message", "Password updated successfully.");  

    }

    public function showAddresses(){
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

            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );
            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            /*--- Get customer addresses ---*/
            $addresses["addresses"] = CustomerAddress::
                where('ca_customer_id', Auth::user()->id)
                ->get()
                ->toArray();


            $addresses["options"] = ShippingFare::orderBy("sf_town")->get();
            
        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.my-account.addresses')
            ->with('customer_information', $customer_information)
            ->with('addresses', $addresses);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.addresses')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information)
                    ->with('addresses', $addresses);
        };
    }

    public function processEditAddress(Request $request){
        $validator = Validator::make($request->all(), [
            'address_town' => 'required',
            'address_details' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessageType = "error_message";
            $errorMessageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $errorMessageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput($request->only('address_town', 'address_details'))->with($errorMessageType, $errorMessageContent);
        }

        //update
        $ca_town_region = explode("||", $request->address_town);
        CustomerAddress::
            where([
                ['id', "=", $request->aid],
                ['ca_customer_id', "=", Auth::user()->id],
            ])
            ->update([
                'ca_region' => $ca_town_region[1]." Region",
                'ca_town' => $ca_town_region[0],
                'ca_address' => $request->address_details
            ]);

        /*--- log activity ---*/
        activity()
        ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Address Updated';
        })
        ->log(Auth::user()->email.' updated their address details.');

        return redirect()->back()->with("success_message", "Address updated successfully.");
    }

    public function showAddAddress(){
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

            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );
            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            /*--- Get customer addresses options---*/
            $address["options"] = ShippingFare::orderBy("sf_town")->get();
            
        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.my-account.address')
            ->with('customer_information', $customer_information)
            ->with('address', $address);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.address')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information)
                    ->with('address', $address);
        };
    }

    public function processAddAddress(Request $request){
        $validator = Validator::make($request->all(), [
            'address_town' => 'required',
            'address_details' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessageType = "error_message";
            $errorMessageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $errorMessageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput($request->only('address_town', 'address_details'))->with($errorMessageType, $errorMessageContent);
        }

        //add address
        $address = new CustomerAddress;
        $ca_town_region = explode("||", $request->address_town);
        $address->ca_customer_id    = Auth::user()->id;
        $address->ca_region         = $ca_town_region[1]." Region";
        $address->ca_town           = $ca_town_region[0];
        $address->ca_address        = $request->address_details;
        $address->save();


        

        if(CustomerAddress::where('ca_customer_id', Auth::user()->id)->get()->count() == 1){
            Customer::find(Auth::user()->id)
            ->update([
                'default_address' => $address->id
            ]);
        }

        /*--- log activity ---*/
        activity()
        ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Address Added';
        })
        ->log(Auth::user()->email.' added an address.');


        return redirect()->route('show.account.addresses')->with("success_message", "Address added successfully.");
    }

    public function showWallet(){
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

            $customer_id = Auth::user()->id;
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
            );
            $customer_information['unread_messages'] = $unread_messages[0]->unread;

            /*--- Get customer wallet options---*/
            $wallet["options"] = WTUPackage::orderBy("wtu_package_cost")->get();
            
        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.my-account.s-wallet')
            ->with('customer_information', $customer_information)
            ->with('wallet', $wallet);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.my-account.s-wallet')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('customer_information', $customer_information)
                    ->with('wallet', $wallet);
        };
    }
    
    public function processWallet(Request $request){
        //check if package exists
        if (is_null(WTUPackage::
            where('id', $request->wtup_id)
            ->first())) {

            return redirect()->back()->with("error_message", "Package not found");
            
        }

        $wtu_package = WTUPackage::
        where('id', $request->wtup_id)
        ->first()
        ->toArray();
        //generate slydepay order

         /*--- log activity ---*/
         activity()
         ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
         ->tap(function(Activity $activity) {
             $activity->subject_type = 'System';
             $activity->subject_id = '0';
             $activity->log_name = 'Wallet Top Up Checkout';
         })
         ->log(Auth::user()->email.' checked out a wallet top up package purchase ['.$wtu_package["wtu_package_description"].']');

        /*--- generate order externally (Slydepay) ---*/
        $slydepay = new Slydepay("ceo@solutekworld.com", "1466854163614");

       
        $order_items = new SlydepayOrderItems([
            new SlydepayOrderItem("Wallet Top Up Package", $wtu_package["wtu_package_description"], $wtu_package["wtu_package_cost"], 1),
        ]);

        $shipping_cost = 0; 
        $tax = 0;

        $wtp_tid = rand(100000, 999999);

        // Create the Order object for this transaction. 
        $slydepay_order = SlydepayOrder::createWithId(
            $order_items,
            $wtp_tid, 
            $shipping_cost,
            $tax,
            "Wallet Top Up on Solushop Ghana",
            "No comment"
        );

        try{
            $response = $slydepay->processPaymentOrder($slydepay_order);
            $redirect_url = $response->redirectUrl();
            $redirect_url_break = explode("=", $redirect_url);

            //generate wallet top up payment
            $WTUPayment = new WTUPayment;
            $WTUPayment->wtu_payment_customer_id    = Auth::user()->id;
            $WTUPayment->wtu_payment_wtup_id        = $request->wtup_id;
            $WTUPayment->wtu_payment_token          = $redirect_url_break[1];
            $WTUPayment->wtu_payment_status         = "UNPAID"; 
            $WTUPayment->wtu_tid                    = $wtp_tid;
            $WTUPayment->save();

            return redirect($redirect_url);
        } catch (Slydepay\Exception\ProcessPayment $e) {
            echo $e->getMessage();
        }
    }
}
