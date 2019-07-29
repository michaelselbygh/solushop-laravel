<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Contracts\Activity;
use \Mobile_Detect;
use Auth;

use App\CartItem;
use App\Coupon;
use App\Customer;
use App\ProductCategory;
use App\SMS;
use App\StockKeepingUnit;
use App\Vendor;
use App\WishlistItem;

class AppGeneralPagesController extends Controller
{
   
    
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

                //Log activity
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

                //Log activity
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
                //Log activity
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

                            //Log activity
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
                                //Log activity
                                activity()
                                ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                                ->tap(function(Activity $activity) {
                                    $activity->subject_type = 'System';
                                    $activity->subject_id = '0';
                                    $activity->log_name = 'Wallet Coupon Redeem Attempt';
                                })
                                ->log(Auth::user()->email.' attempted to redeem already used coupon '.$coupon->coupon_code);

                                return redirect()->back()->with('error_message', 'Coupon already redeemed.');
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

                                //update coupon state
                                $coupon_code = $coupon->coupon_code;
                                $coupon_value = $coupon->coupon_value;
                                $coupon->coupon_state = 3;
                                $coupon->save();

                                //notify customer
                                //queue customer message
                                $sms_message = "Hi ".ucwords(strtolower(Auth::user()->first_name)).", you have successfully redeemed a coupon worth $coupon_value Cedis. Your account balance is now GHS $newCustomerBalance";
                                $sms_phone = Auth::user()->phone;

                                $sms = new SMS;
                                $sms->sms_message = $sms_message;
                                $sms->sms_phone = $sms_phone;
                                $sms->sms_state = 1;
                                $sms->save();

                                //Log activity
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
            //Log activity
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
}
