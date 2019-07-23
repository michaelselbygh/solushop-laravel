<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppShopController extends Controller
{
    public function showError404Page(){
        return view('app.main.general.404');
    }
}
