<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryPartnerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:delivery-partner');
    }

    public function index()
    {
        return view('portal.main.delivery-partner.dashboard');
    }
}
