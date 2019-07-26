<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductCategory;

use \Mobile_Detect;
use Session;

class AppShopController extends Controller
{
    public function showProducts(){
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
                ->with('search_bar_pc_options', $search_bar_pc_options);
    }
    public function showError404Page(){
        return view('app.main.general.404');
    }
}
