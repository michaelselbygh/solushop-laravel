<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\Vendor;
use App\Product;
use App\ProductCategory;

use \Mobile_Detect;
use Session;

class AppVendorController extends Controller
{
    public function showVendor($vendorSlug){
        /*--- Vendor Check ---*/
        if (is_null(Vendor::where('username', $vendorSlug)->first())) {
            //vendor doesnt exist
            return redirect()->route('page.not.found');
        }

        $vendorObject = DB::select(
            "SELECT vendors.id, name, username FROM vendors, vendor_subscriptions  where vendors.id = vendor_subscriptions.vs_vendor_id and vendor_subscriptions.vs_days_left > 0 and vendors.username = :vendor_slug",
            ['vendor_slug' => $vendorSlug]
        );

        $vendor['id'] = $vendorObject[0]->id;
        $vendor['name'] = $vendorObject[0]->name;
        $vendor['username'] = $vendorObject[0]->username;

        /*--- Get Vendor Details and check for subscription ---*/
        if (empty($vendorObject)) {
            //vendor not subscribed
            return redirect()->route('page.not.found');
        }

        
        /*--- Get Vendor Products---*/

        //randomization occurs every hour
        if (null != (Session::get('seed'))) {
            if((time() - Session::get('seed')) > 3600){
                Session::put('seed', time());
            }
        }else{
            Session::put('seed', time());
        }
        $vendor['products'] = Product::
                inRandomOrder(Session::get('seed'))
                ->where('product_vid', $vendor['id']) 
                ->where('product_state', '1') 
                ->with('images')
                ->paginate(30)
                ->onEachSide(2);

         //process vendor joined date
         $vendor['vendor_date_joined'] = date('F Y', strtotime(substr($vendor['id'], 0, 2)."-".substr($vendor['id'], 2, 2)."-".substr($vendor['id'], 4, 4)));

         /*--- vendor sales and ratings ---*/
        $vendorPurchases = DB::select(
            "SELECT sum(oi_quantity) purchases from order_items, orders, stock_keeping_units, products where stock_keeping_units.sku_product_id = products.id and order_items.oi_order_id = orders.id and order_items.oi_sku = stock_keeping_units.id and products.product_vid = :vendor_id and ( order_items.oi_state = '2' OR order_items.oi_state = '3' OR order_items.oi_state = '4')",
            ['vendor_id' => $vendor['id']]
        );

        $vendor['vendor_purchases'] = $vendorPurchases[0]->purchases;

        $vendorReviews = DB::select(
            "SELECT sum(pr_rating) as rating, count(pr_rating) as rating_count from product_reviews, products where product_reviews.pr_product_id = products.id and products.product_vid = :vendor_id",
            ['vendor_id' => $vendor['id']]
        );

        $vendor['vendor_sales_and_rating_header'] = "";
        if($vendor['vendor_purchases'] > 0){
            $vendor['vendor_sales_and_rating_header'] .= "Vendor Sales";
        }

        if ($vendorReviews[0]->rating_count > 0) {
            $vendor['vendor_rating_count'] = $vendorReviews[0]->rating_count;
            $vendor['vendor_rating'] = $vendorReviews[0]->rating / $vendorReviews[0]->rating_count * 20;
            $vendor['vendor_sales_and_rating_header'] .= "& Rating";
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.vendor')
                ->with('vendor', $vendor);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.vendor')
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('vendor', $vendor);
        }

        

    }

    public function showVendors(){
        /*--- Get Active Vendors with products  ---*/
        //randomization occurs every hour
        if (null != (Session::get('seed'))) {
            if((time() - Session::get('seed')) > 3600){
                Session::put('seed', time());
            }
        }else{
            Session::put('seed', time());
        }


        $vendors = DB::select(
            "SELECT *, vendors.id as vendor_id from vendors, vendor_subscriptions, vs_packages  where vendor_subscriptions.vs_vsp_id = vs_packages.id AND vendors.id = vendor_subscriptions.vs_vendor_id AND vs_days_left > 0 ORDER BY vendors.username"
        );


        for ($i = 0; $i < sizeof($vendors); $i++) {
            

            $vendors[$i]->vendor_date_joined = date('F Y', strtotime(substr($vendors[$i]->vendor_id, 0, 2)."-".substr($vendors[$i]->vendor_id, 2, 2)."-".substr($vendors[$i]->vendor_id, 4, 4)));
            /*--- vendor sales and ratings ---*/
            $vendorPurchases = DB::select(
                "SELECT sum(oi_quantity) purchases from order_items, orders, stock_keeping_units, products where stock_keeping_units.sku_product_id = products.id and order_items.oi_order_id = orders.id and order_items.oi_sku = stock_keeping_units.id and products.product_vid = :vendor_id and ( order_items.oi_state = '2' OR order_items.oi_state = '3' OR order_items.oi_state = '4')",
                ['vendor_id' => $vendors[$i]->vendor_id]
            );

            $vendors[$i]->vendor_purchases = $vendorPurchases[0]->purchases;

            $vendorReviews = DB::select(
                "SELECT sum(pr_rating) as rating, count(pr_rating) as rating_count from product_reviews, products where product_reviews.pr_product_id = products.id and products.product_vid = :vendor_id",
                ['vendor_id' => $vendors[$i]->vendor_id]
            );


            $vendors[$i]->vendor_sales_and_rating_header = "";
            if($vendors[$i]->vendor_purchases > 0){
                $vendors[$i]->vendor_sales_and_rating_header .= "Vendor Sales";
            }

            if ($vendorReviews[0]->rating_count > 0) {
                $vendors[$i]->vendor_rating_count = $vendorReviews[0]->rating_count;
                $vendors[$i]->vendor_rating = $vendorReviews[0]->rating / $vendorReviews[0]->rating_count * 20;
                $vendors[$i]->vendor_sales_and_rating_header .= "& Rating";
            }
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){

            return view('mobile.main.general.vendors')
                    ->with('vendors', $vendors);
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);

            return view('app.main.general.vendors')
                    ->with('search_bar_pc_options', $search_bar_pc_options)
                    ->with('vendors', $vendors);
        }

    }
}
