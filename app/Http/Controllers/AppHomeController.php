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

class AppHomeController extends Controller
{
    public function showHome()
    {
        

        /*---selecting search bar categories (level 2 categories)---*/
        $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['pc_id', 'pc_description', 'pc_slug']);

        /*---creating array for the category side bar---*/
        $side_bar_pc_options = array();
        $i = 0;
        foreach ($search_bar_pc_options as $search_bar_pc_option)  { 
            $side_bar_pc_options[$i]['pc_id'] = $search_bar_pc_option->pc_id;
            $side_bar_pc_options[$i]['pc_slug'] = $search_bar_pc_option->pc_slug;
            $side_bar_pc_options[$i]['pc_description'] = $search_bar_pc_option->pc_description;

            //get sub categories
            $side_bar_spc_options = ProductCategory::
                where('pc_parent', $side_bar_pc_options[$i]['pc_id']) 
                ->orderBy('pc_description')   
                ->get(['pc_id', 'pc_description', 'pc_slug']);

            $j = 0;
            foreach ($side_bar_spc_options as $side_bar_spc_option)  { 
                $side_bar_pc_options[$i]['pc_sub_category'][$j]['pc_id'] = $side_bar_spc_option->pc_id;
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
                whereIn('pc_id', $sections['id_init'])
                ->get(['pc_id', 'pc_description', 'pc_slug', 'pc_level'])
                ->toArray();


        shuffle($sectionCategoryObjects);

        for ($i=0; $i < sizeof($sectionCategoryObjects); $i++) {
            $sections['id'][$i]             = $sectionCategoryObjects[$i]['pc_id'];
            $sections['description'][$i]    = $sectionCategoryObjects[$i]['pc_description'];
            $sections['slug'][$i]           = $sectionCategoryObjects[$i]['pc_slug'];
            $sections['products'][$i]       = [];

            $productCount = 0;
            //get products
            $sectionCategoryProducts        = Product::
                where('product_cid', $sections['id'][$i]) 
                ->where('product_state', '1') 
                ->with('product_images')
                ->get()
                ->toArray();

            //get vendor slug for each product | Add to product | Add to product array for that category
            for ($j=0; $j < sizeof($sectionCategoryProducts); $j++) { 
                $vendors = Vendor::
                    where('vendor_id', $sectionCategoryProducts[$j]['product_vid'])
                    ->get()
                    ->toArray();

                $sectionCategoryProducts[$j]['vendor_slug'] = $vendors[0]['username'];
                $sections['products'][$i][$productCount] = $sectionCategoryProducts[$j];
                $productCount++;
            }
            
           

            //adding products from sub categories
            if ($sectionCategoryObjects[$i]['pc_level'] == 2) {
                $sectionSubCategoryObjects = ProductCategory::
                where('pc_parent', $sections['id'][$i])
                ->get(['pc_id'])
                ->toArray();

                for ($k=0; $k < sizeof($sectionSubCategoryObjects); $k++) {
                    //get products
                        $sectionCategoryProducts        = Product::
                        where('product_cid', $sectionSubCategoryObjects[$k]['pc_id']) 
                        ->where('product_state', '1') 
                        ->with('product_images')
                        ->get()
                        ->toArray();

                    //get vendor slug for each product | Add to product | Add to product array for that category
                    for ($l=0; $l < sizeof($sectionCategoryProducts); $l++) { 
                        $vendors = Vendor::
                            where('vendor_id', $sectionCategoryProducts[$l]['product_vid'])
                            ->get()
                            ->toArray();

                        $sectionCategoryProducts[$l]['vendor_slug'] = $vendors[0]['username'];
                        $sections['products'][$i][$productCount] = $sectionCategoryProducts[$l];
                        $productCount++;
                    }
                }
            }    
            
            shuffle($sections['products'][$i]);
        }
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            $view = 'mobile.main.general.home';
        }else{
            $view = 'app.main.general.home';
        }
        return view($view)
                ->with('sections', $sections)
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('side_bar_pc_options', $side_bar_pc_options);
    }
}
