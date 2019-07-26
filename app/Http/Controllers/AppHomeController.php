<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Mobile_Detect;


use App\Customer;
use App\Chocolate;
use App\Milk;
use App\Product;
use App\ProductCategory;
use App\ProductImage;
use App\Vendor;

use Session;

class AppHomeController extends Controller
{
    public function showHome()
    {
        

        /*---selecting search bar categories (level 2 categories)---*/
        $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);

        /*---selecting featured products ---*/
         //randomization occurs every hour
         if (null != (Session::get('seed'))) {
            if((time() - Session::get('seed')) > 3600){
                Session::put('seed', time());
            }
        }else{
            Session::put('seed', time());
        }
        $featured_products = Product::
                inRandomOrder(Session::get('seed'))
                ->where('product_state', '1') 
                ->with('vendor', 'images')
                ->take(3)
                ->get()
                ->toArray();


        /*---creating array for the category side bar---*/
        $side_bar_pc_options = array();
        $i = 0;
        foreach ($search_bar_pc_options as $search_bar_pc_option)  { 
            $side_bar_pc_options[$i]['id'] = $search_bar_pc_option->id;
            $side_bar_pc_options[$i]['pc_slug'] = $search_bar_pc_option->pc_slug;
            $side_bar_pc_options[$i]['pc_description'] = $search_bar_pc_option->pc_description;

            //get sub categories
            $side_bar_spc_options = ProductCategory::
                where('pc_parent', $side_bar_pc_options[$i]['id']) 
                ->orderBy('pc_description')   
                ->get(['id', 'pc_description', 'pc_slug']);

            $j = 0;
            foreach ($side_bar_spc_options as $side_bar_spc_option)  { 
                $side_bar_pc_options[$i]['pc_sub_category'][$j]['id'] = $side_bar_spc_option->id;
                $side_bar_pc_options[$i]['pc_sub_category'][$j]['pc_slug'] = $side_bar_spc_option->pc_slug;
                $side_bar_pc_options[$i]['pc_sub_category'][$j]['pc_description'] = $side_bar_spc_option->pc_description;
                $j++;
            }
            //increment level two category counter
            $i++;
        }

        /*---selecting home page categories and products---*/
        $sections       = array();
        $sections['id_init'] = ['27', '94', '89', '69', '75', '16', '31', '54', '62', '41', '9'];

        $sectionCategoryObjects = ProductCategory::
                whereIn('id', $sections['id_init'])
                ->get(['id', 'pc_description', 'pc_slug', 'pc_level', 'pc_cna'])
                ->toArray();


        shuffle($sectionCategoryObjects);

        for ($i=0; $i < sizeof($sectionCategoryObjects); $i++) {
            $sections['id'][$i]             = $sectionCategoryObjects[$i]['id'];
            $sections['description'][$i]    = $sectionCategoryObjects[$i]['pc_description'];
            $sections['slug'][$i]           = $sectionCategoryObjects[$i]['pc_slug'];     

            $associatedCategories = explode("|", $sectionCategoryObjects[$i]['pc_cna']);
            $associatedCategories[] = $sectionCategoryObjects[$i]['id'];

            //randomization occurs every hour
            if (null != (Session::get('seed'))) {
                if((time() - Session::get('seed')) > 3600){
                    Session::put('seed', time());
                }
            }else{
                Session::put('seed', time());
            }

            //get products
            $sections['products'][$i]        = Product::
                inRandomOrder(Session::get('seed')) 
                ->whereIn('product_cid', $associatedCategories)
                ->where('product_state', '1') 
                ->with('images', 'vendor')
                ->get()
                ->toArray();
            
        }
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            $view = 'mobile.main.general.home';
        }else{
            $view = 'app.main.general.home';
        }

        return view($view)
                ->with('sections', $sections)
                ->with('featured_products', $featured_products)
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('side_bar_pc_options', $side_bar_pc_options);
    }
}
