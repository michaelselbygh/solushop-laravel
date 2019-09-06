<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Customer;
use App\Product;
use App\ProductCategory;

use Auth;
use \Mobile_Detect;
use Session;

class AppShopController extends Controller
{
    public function showProducts(){
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

        /*--- Get products ---*/
        //randomization occurs every hour
        if (null != (Session::get('seed'))) {
            if((time() - Session::get('seed')) > 3600){
                Session::put('seed', time());
            }
        }else{
            Session::put('seed', time());
        }
        $product = Product::
                inRandomOrder(Session::get('seed'))
                ->where('product_state', '1') 
                ->with('vendor', 'images', 'skus', 'reviews')
                ->paginate(30)
                ->onEachSide(2);

        /*---selecting search bar categories (level 2 categories)---*/
        $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            $view = 'mobile.main.general.shop';
        }else{
            $view = 'app.main.general.shop';
        }

        return view($view)
                ->with('product', $product)
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('customer_information', $customer_information);
    }

    public function showCategoryProducts($categorySlug){
        
        
        //check for Category
        if (is_null(ProductCategory::where('pc_slug', $categorySlug)->first())) {
            return redirect()->route('page.not.found');
        }

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

        $category = ProductCategory::where('pc_slug', $categorySlug)->first()->toArray();
        
        /*--- Get products ---*/
        //randomization occurs every hour
        if (null != (Session::get('seed'))) {
            if((time() - Session::get('seed')) > 3600){
                Session::put('seed', time());
            }
        }else{
            Session::put('seed', time());
        }

        $associatedCategories = explode("|", $category['pc_cna']);
        $associatedCategories[] = $category['id'];

        
        //get products
        $category['products']        = Product::
        inRandomOrder(Session::get('seed'))
        ->whereIn('product_cid', $associatedCategories) 
        ->where('product_state', '1') 
        ->with('images', 'vendor')
        ->paginate(30)
        ->onEachSide(2);

        if(sizeof($category['products']) == 0){
            return redirect()->route('page.not.found');
        }
        $product = $category['products'];

        /*---selecting search bar categories (level 2 categories)---*/
        $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);


        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            $view = 'mobile.main.general.shop';
            return view('mobile.main.general.shop')
                ->with('category', $category)
                ->with('product', $product)
                ->with('customer_information', $customer_information);
        }else{
            return view('app.main.general.shop')
                ->with('category', $category)
                ->with('product', $product)
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('customer_information', $customer_information);
        }
    }

    public function showSearchProducts(Request $request){
        //checks
        if (!isset($request->search_query_string) OR trim($request->search_query_string) == "") {
                return redirect('shop');
        }else{
            
            /*--- Get search results products ---*/
            $search_result_product_ids_object = DB::select(
                "SELECT products.ID as product_id from products, vendors, product_categories WHERE product_categories.id = products.product_cid AND products.product_vid = vendors.ID AND (product_name LIKE CONCAT('%', :search_string_param_1, '%') OR product_tags LIKE CONCAT('%', :search_string_param_2, '%') OR product_features LIKE CONCAT('%', :search_string_param_3, '%') OR product_description  LIKE CONCAT('%', :search_string_param_4, '%') OR product_categories.pc_description  LIKE CONCAT('%', :search_string_param_5, '%') OR vendors.name  LIKE CONCAT('%', :search_string_param_6, '%') OR vendors.username  LIKE CONCAT('%', :search_string_param_7, '%')) AND product_state = '1'",
                [
                    'search_string_param_1' => $request->search_query_string,
                    'search_string_param_2' => $request->search_query_string,
                    'search_string_param_3' => $request->search_query_string,
                    'search_string_param_4' => $request->search_query_string,
                    'search_string_param_5' => $request->search_query_string,
                    'search_string_param_6' => $request->search_query_string,
                    'search_string_param_7' => $request->search_query_string,
                ]
            );
            

            for ($i=0; $i < sizeof($search_result_product_ids_object); $i++) { 
                $search_result_product_ids[$i] = $search_result_product_ids_object[$i]->product_id;
            }
            

            if (!isset($search_result_product_ids)) {
                return redirect()->route('page.not.found');
            }

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

            /*--- Get products ---*/
            //randomization occurs every hour
            if (null != (Session::get('seed'))) {
                if((time() - Session::get('seed')) > 3600){
                    Session::put('seed', time());
                }
            }else{
                Session::put('seed', time());
            }
            $product = Product::
                inRandomOrder(Session::get('seed'))
                ->whereIn('id', $search_result_product_ids)
                ->where('product_state', '1') 
                ->with('vendor', 'images', 'skus', 'reviews')
                ->paginate(500)
                ->onEachSide(2);
        }
        

        /*---selecting search bar categories (level 2 categories)---*/
        $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            $view = 'mobile.main.general.shop';
        }else{
            $view = 'app.main.general.shop';
        }

        return view($view)
                ->with('product', $product)
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('customer_information', $customer_information);
    }
}
