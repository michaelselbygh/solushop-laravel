<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\DB;
use \Mobile_Detect;
use Auth;

use App\CartItem;
use App\Conversation;
use App\Coupon;
use App\Customer;
use App\CustomerAddress;
use App\Message;
use App\Order;
use App\OrderItem;
use App\Product;
use App\ProductCategory;
use App\SMS;
use App\StockKeepingUnit;
use App\Vendor;
use App\WishlistItem;

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

        return redirect()->back()->with("success_message", "Message sent successfully.");
    }

    public function showPersonalDetails(){
        
    }

    public function processPersonalDetails(Request $request){
        
    }

    public function showOrders(){
        
    }

    public function showOrder($orderID){
        
    }

    public function processOrder(Request $request){
        
    }

    public function showLoginAndSecurity(){
        
    }

    public function processLoginAndSecurity(Request $request){
        
    }

    public function showAddresses(){
        
    }

    public function showAddAddress(){
        
    }

    public function processAddAddress(Request $request){
        
    }

    public function showEditAddress($addressID){
        
    }

    public function processEditAddress(Request $request){
        
    }

    public function showWallet(){
        
    }
    
    public function processWallet(Request $request){
        
    }
}
