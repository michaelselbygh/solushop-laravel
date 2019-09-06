<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use \Mobile_Detect;
use Auth;

use App\CartItem;
use App\Customer;
use App\OrderItem;
use App\Product;
use App\ProductCategory;
use App\ProductReview;
use App\StockKeepingUnit;
use App\WishlistItem;
use App\Vendor;

class AppProductController extends Controller
{
    public function showProduct($vendorSlug, $productSlug){

        //check for vendor or product
        if (is_null(Vendor::where('username', $vendorSlug)->first()) OR is_null(Product::where('product_slug', $productSlug) ->where('product_state', '1') ->first())) {
            return redirect()->route('page.not.found');
        }

        //get vendor
        $vendor = Vendor::
            where('username', $vendorSlug)
            ->get(['id'])
            ->toArray();
        
        if($vendor){
            //get product with images
            $product = Product::
                where('product_slug', $productSlug) 
                ->where('product_state', '1') 
                ->with('vendor', 'images', 'skus', 'category', 'reviews')
                ->first()
                ->toArray();

            

            if($product){
                /*---generate breadcrumb of categories--*/
                $productCategoryBreadcrumb[0]['url'] = "shop/category/".$product['category'][0]['pc_slug'];
                $productCategoryBreadcrumb[0]['description'] = $product['category'][0]['pc_description'];
                

                //get parent categories
                $productParentCategoryID = $product['category'][0]['pc_parent'];
                for ($i=1; $i < $product['category'][0]['pc_level']; $i++) { 
                    $productParentCategory = ProductCategory::
                    where('id', $productParentCategoryID) 
                    ->first()
                    ->toArray();

                    $productCategoryBreadcrumb[$i]['url'] = "shop/category/".$productParentCategory['pc_slug'];
                    $productCategoryBreadcrumb[$i]['description'] = $productParentCategory['pc_description'];

                    $productParentCategoryID = $productParentCategory['pc_parent'];

                }

                $product['breadcrumb'] = $productCategoryBreadcrumb;

                /*---Get stock status | Should the variations section show or not--*/
                $productSKUs = StockKeepingUnit::
                    where([
                        ['sku_product_id', '=', $product['id']],
                        ['sku_stock_left', '>', 0]
                    ])
                    ->get()
                    ->toArray();

                $productStockStatus = 1;
                if (sizeof($productSKUs) > 0) {
                    $productStockStatus = 0;
                }

                $variationShow = 1;
                if (sizeof($productSKUs) > 1 OR (sizeof($productSKUs) == 1 && $productSKUs[0]['sku_variant_description'] != "None")) {
                    $variationShow = 0;
                }

                /*---Getting index of first SKU with stock ---*/
                for ($i=0; $i < sizeof($product['skus']); $i++) { 
                    if ($product['skus'][$i]['sku_stock_left'] > 0) {
                        $product['sku_first'] = $i;
                        break;
                    }
                }

                $product['variation_show'] = $variationShow;
                $product['stock_status'] = $productStockStatus;

                /*---Build ID Array of available SKU--*/
                $productSKUID = [];
                for ($i=0; $i < sizeof($product['skus']); $i++) { 
                    $productSKUID[$i] = $product['skus'][$i]['id'];
                }

                /*---get product purchases--*/
                $productPurchase = OrderItem::
                    whereIn('oi_sku', $productSKUID)
                    ->whereIn('oi_state', ['2', '3', '4'])
                    ->sum('oi_quantity');

                $product['purchases'] = $productPurchase;

                //get review customer
                for ($i=0; $i < sizeof($product['reviews']); $i++) { 
                    $product['reviews'][$i]['customer'] = Customer::
                    where('id', $product['reviews'][$i]['pr_customer_id'])
                    ->get()
                    ->toArray();
                }

                $product['sales_and_rating_header'] = "";
                if($product['purchases'] > 0){
                    $product['sales_and_rating_header'] .= "Product Sales";
                }

                if(sizeof($product['reviews']) > 0){
                    $product['sales_and_rating_header'] .= "& Rating";
                }

                


                //reformatting product highlighted features
                $product['product_features'] = explode("|", $product['product_features']);


                /*---Get delivery duration estimate,  vendor date joined, and vendor type ---*/
                //date processing
                function isWeekend($date) {
                    return (date('N', strtotime($date)) >= 6);
                }

                $ADT = $product['product_dd']. " business day";
                if($product['product_dd'] > 1){
                    $ADT .= "s";
                }

                $product['avg_dd_estimate'] = $ADT;
               
                


                $date = date('Y-m-d');
                if($product['product_dd'] > 0){
                    $lbday = $product['product_dd'] - 1;
                }else{
                    $lbday = $product['product_dd'];
                }

                $ubday = $product['product_dd'] + 1;
                $lbdate = date('D j M', strtotime($date . " +$lbday weekday"));
                $ubdate = date('D j M', strtotime($date . " +$ubday weekday"));

                $product['avg_dd_date_upper'] = $lbdate;
                $product['avg_dd_date_lower'] = $ubdate;

                //process vendor joined date
                $product['vendor_date_joined'] = date('F Y', strtotime(substr($product['product_vid'], 0, 2)."-".substr($product['product_vid'], 2, 2)."-".substr($product['product_vid'], 4, 4)));

                /*--- product sales and ratings ---*/
                if(sizeof($product['reviews']) > 0){
                    $product['rating'] = 0;
                    for ($i=0; $i < sizeof($product['reviews']); $i++) { 
                        $product['rating'] += $product['reviews'][$i]['pr_rating'];
                    }

                    $product['rating'] = $product['rating'] / $i;
                }

                /*--- vendor sales and ratings ---*/
                $vendorPurchases = DB::select(
                    "SELECT sum(oi_quantity) purchases from order_items, orders, stock_keeping_units, products where stock_keeping_units.sku_product_id = products.id and order_items.oi_order_id = orders.id and order_items.oi_sku = stock_keeping_units.id and products.product_vid = :vendor_id and ( order_items.oi_state = '2' OR order_items.oi_state = '3' OR order_items.oi_state = '4')",
                    ['vendor_id' => $product['product_vid']]
                );

                $product['vendor_purchases'] = $vendorPurchases[0]->purchases;

                $vendorReviews = DB::select(
                    "SELECT sum(pr_rating) as rating, count(pr_rating) as rating_count from product_reviews, products where product_reviews.pr_product_id = products.id and products.product_vid = :vendor_id",
                    ['vendor_id' => $product['product_vid']]
                );

                $product['vendor_sales_and_rating_header'] = "";
                if($product['vendor_purchases'] > 0){
                    $product['vendor_sales_and_rating_header'] .= "Vendor Sales";
                }

                if ($vendorReviews[0]->rating_count > 0) {
                    $product['vendor_rating_count'] = $vendorReviews[0]->rating_count;
                    $product['vendor_rating'] = $vendorReviews[0]->rating / $vendorReviews[0]->rating_count * 20;
                    $product['vendor_sales_and_rating_header'] .= "& Rating";
                }
                
                /*--- Select customer names for reviews | check if logged in customer has a comment | check if signed in user has made a purchase ---*/
                $product['signed_in_customer_review'] = 1;
                $product['signed_in_customer_review_rating'] = 5;
                $product['signed_in_customer_review_edited'] = "New";
                for ($i=0; $i < sizeof($product["reviews"]); $i++) { 
                    if (Auth::check() AND $product['reviews'][$i]['pr_customer_id'] == Auth::user()->id){
                        $product['signed_in_customer_review'] = 0;
                        $product['signed_in_customer_review_comment']   = $product['reviews'][$i]['pr_comment'];
                        $product['signed_in_customer_review_rating']    = $product['reviews'][$i]['pr_rating'];

                        if ($product['reviews'][$i]['pr_edited'] == 1) {
                            $product['signed_in_customer_review_edited'] = "Edited";
                        }
                    }

                    $reviewCustomer = Customer::
                        where('id', $product['reviews'][$i]['pr_customer_id'])
                        ->first()
                        ->toArray();

                    $product['reviews'][$i]['customer'] = $reviewCustomer;
                }

                $product['signed_in_customer_purchase'] = 1;
                if (Auth::check()) {
                    $customerPurchases = DB::select(
                        "SELECT * from orders, order_items, stock_keeping_units where orders.id = order_items.oi_order_id and order_items.oi_sku = stock_keeping_units.id and orders.order_customer_id = :customer_id and ( oi_state = 2 OR oi_state = 3 OR oi_state = 4) and stock_keeping_units.sku_product_id = :product_id",
                        ['customer_id' => Auth::user()->id, 'product_id' => $product['id']]
                    );
                }

                if(isset($customerPurchases) AND $customerPurchases != null){
                    $product['signed_in_customer_purchase'] = 0;
                }

                /*--- Select product related products --- */
                $productRelated = Product::
                where('product_cid', $product['product_cid']) 
                ->where('product_state', '1') 
                ->with('vendor', 'images')
                ->limit(10)
                ->get()
                ->toArray();

                $product['related_products'] = $productRelated;

                /*--- Update product views before redirect ---*/
                $productViewsUpdate = Product::
                where('product_slug', $productSlug) 
                ->first();
                $productViewsUpdate->product_views++;
                $productViewsUpdate->save();

                /*---selecting search bar categories (level 2 categories)---*/
                $search_bar_pc_options = ProductCategory::
                where('pc_level', 2) 
                ->orderBy('pc_description')   
                ->get(['id', 'pc_description', 'pc_slug']);

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
                    $customer_information['whatsapp_name'] = Auth::user()->first_name." ".Auth::user()->last_name;

                }else{
                    $customer_information['wallet_balance'] = 0;
                    $customer_information['cart_count'] = 0;
                    $customer_information['wishlist_count'] = 0;
                    $customer_information['whatsapp_name'] = "";
                }
                
                $detect = new Mobile_Detect;
                if( $detect->isMobile() && !$detect->isTablet() ){
                    $view = 'mobile.main.general.product';
                }else{
                    $view = 'app.main.general.product';
                }

                
                return view($view)
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('product', $product)
                ->with('customer_information', $customer_information);
                
            }
        }
      

        

        
    }

    public function processProductAction(Request $request){
        if(isset($request->product_action)){
            $product = [];
            if($request->product_action == "add_to_cart"){
                if (!Auth::check()) {
                    $messageType = 'error_message';
                    $messageContent = "Login to access your cart.";
                } else {
                    $product['id_check'] = Product::
                        where('id', $request->product_id)
                        ->where('product_state', '1')
                        ->first();

                    if ($product['id_check'] == null) {
                        //product does not exist
                        $messageType = 'error_message';
                        $messageContent = "Sorry, product not found.";
                    } else {
                        $product['name'] = ucwords($product['id_check']->product_name);
                        $product['cart_check'] = CartItem::
                            where('ci_customer_id', Auth::user()->id)
                            ->where('ci_sku', $request->product_sku)
                            ->first();

                        
                        if (!is_null($product['cart_check'])) { 
                            //Item already in customers cart.
                            $messageType = 'error_message';
                            $messageContent = 'Product already found in cart.';
                        }else{

                            $cartItem = new CartItem;
                            $cartItem->ci_customer_id = Auth::user()->id;
                            $cartItem->ci_sku = $request->product_sku;
                            $cartItem->ci_quantity = $request->product_quantity;
                            $cartItem->save();

                            /*--- log activity ---*/
                            activity()
                            ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                            ->tap(function(Activity $activity) {
                                $activity->subject_type = 'System';
                                $activity->subject_id = '0';
                                $activity->log_name = 'Cart Item Added';
                            })
                            ->log(Auth::user()->email.' added item '.$request->product_sku.' to cart.');

                            $messageType = 'success_message';
                            $messageContent = 'Product added to cart.';
                        }
                    }
                }
            }elseif($request->product_action == "add_to_wishlist"){

                if (!Auth::check()) {
                    $messageType = 'error_message';
                    $messageContent = "Login to access your wishlist.";
                } else {
                    $product['id_check'] = Product::
                        where('id', $request->product_id)
                        ->where('product_state', '1')
                        ->first();

                    if ($product['id_check'] == null) {
                        //product does not exist
                        $messageType = 'error_message';
                        $messageContent = "Sorry, product not found.";
                    } else {
                        $product['name'] = ucwords($product['id_check']->product_name);
                        $product['wishlist_check'] = WishlistItem::
                            where('wi_customer_id', Auth::user()->id)
                            ->where('wi_product_id', $request->product_id)
                            ->first();

                        
                        if (!is_null($product['wishlist_check'])) { 
                            //Item already in customers wishlist.
                            $messageType = 'error_message';
                            $messageContent = 'Product already found in wishlist.';
                        }else{

                            $wishlistItem = new WishlistItem;
                            $wishlistItem->wi_customer_id = Auth::user()->id;
                            $wishlistItem->wi_product_id = $request->product_id;
                            $wishlistItem->save();

                            /*--- log activity ---*/
                            activity()
                            ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                            ->tap(function(Activity $activity) {
                                $activity->subject_type = 'System';
                                $activity->subject_id = '0';
                                $activity->log_name = 'Wishlist Item Added';
                            })
                            ->log(Auth::user()->email.' added item '.$request->product_id.' to wishlist.');

                            $messageType = 'success_message';
                            $messageContent = 'Product added to wishlist.';
                        }
                    }
                }
            }elseif($request->product_action == "add_review"){

                if (ProductReview::where('pr_customer_id', Auth::user()->id)->where('pr_product_id', $request->pid)->first()) {
                    $product_review = ProductReview::where('pr_customer_id', Auth::user()->id)->where('pr_product_id', $request->pid)->first();
                    $product_review->pr_edited = 1;
                    $product_review->pr_rating = $request->ratingValue;
                    $product_review->pr_comment = $request->message;
                    $product_review->save();

                    /*--- Log activity ---*/
                    activity()
                    ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                    ->tap(function(Activity $activity) {
                        $activity->subject_type = 'System';
                        $activity->subject_id = '0';
                        $activity->log_name = 'Product Review Edited';
                    })
                    ->log(Auth::user()->email.' edited review on product '.$request->pid);

                    $messageType = 'success_message';
                    $messageContent = 'Product review edited successfully.';

                }else{
                    $product_review =  new ProductReview;
                    $product_review->pr_customer_id = Auth::user()->id;
                    $product_review->pr_product_id = $request->pid;
                    $product_review->pr_edited = 0;
                    $product_review->pr_rating = $request->ratingValue;
                    $product_review->pr_comment = $request->message;
                    $product_review->pr_date = date("M j, Y");
                    $product_review->save();

                    /*--- Log activity ---*/
                    activity()
                    ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                    ->tap(function(Activity $activity) {
                        $activity->subject_type = 'System';
                        $activity->subject_id = '0';
                        $activity->log_name = 'Product Review Added';
                    })
                    ->log(Auth::user()->email.' added review on product '.$request->pid);

                    $messageType = 'success_message';
                    $messageContent = 'Product review added successfully.';
                }
            }
        }else{
            $messageType = 'error_message';
            $messageContent = 'Something went wrong. Please try again.';
        }

        return redirect()->back()->with($messageType, $messageContent);
    }
}
