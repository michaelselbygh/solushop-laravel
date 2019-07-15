<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesAssociateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sales-associate');
    }

    public function index()
    {
        return view('portal.main.sales-associate.dashboard');
    }
}
