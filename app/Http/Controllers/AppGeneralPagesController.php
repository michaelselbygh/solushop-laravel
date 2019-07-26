<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Mobile_Detect;

use App\ProductCategory;

class AppGeneralPagesController extends Controller
{
    public function showAbout(){
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.about');
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.about')
                    ->with('search_bar_pc_options', $search_bar_pc_options);
        }

        
    }

    public function showContact(){
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.contact');
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.contact')
                    ->with('search_bar_pc_options', $search_bar_pc_options);
        }
    }

    public function showTNC(){
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.terms-and-conditions');
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.terms-and-conditions')
                    ->with('search_bar_pc_options', $search_bar_pc_options);
        }
    }

    public function showPrivacyPolicy(){
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.privacy-policy');
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.privacy-policy')
                    ->with('search_bar_pc_options', $search_bar_pc_options);
        }
    }

    public function showReturnPolicy(){
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.return-policy');
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.return-policy')
                    ->with('search_bar_pc_options', $search_bar_pc_options);
        }
    }

    public function showFAQ(){
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.faq');
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.faq')
                    ->with('search_bar_pc_options', $search_bar_pc_options);
        };
    }

    public function show404(){
        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.404');
        }else{
            /*---selecting search bar categories (level 2 categories)---*/
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')   
            ->get(['id', 'pc_description', 'pc_slug']);
            return view('app.main.general.404')
                    ->with('search_bar_pc_options', $search_bar_pc_options);
        };
    }
}
