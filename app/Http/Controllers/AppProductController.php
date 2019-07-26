<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use \Mobile_Detect;
use Auth;

use App\Customer;
use App\Vendor;
use App\Product;
use App\ProductCategory;
use App\ProductReview;
use App\OrderItem;
use App\StockKeepingUnit;

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
                
                $detect = new Mobile_Detect;
                if( $detect->isMobile() && !$detect->isTablet() ){
                    $view = 'mobile.main.general.product';
                }else{
                    $view = 'app.main.general.product';
                }


                return view($view)
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('product', $product);
            }
        }
      

        

        
    }
}
