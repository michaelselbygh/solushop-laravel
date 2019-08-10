<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\AccountTransaction;
use App\Order;
use App\SalesAssociate;

class SalesAssociateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sales-associate');
    }

    public function index()
    {
        if (is_null(SalesAssociate::where('id', Auth::guard('sales-associate')->user()->id)->first())) {
            return redirect()->route("manager.show.sales.associates")->with("error_message", "Sales associate not found.");
        }

        $sales_associate = SalesAssociate::where('id', Auth::guard('sales-associate')->user()->id)->with('badge_info')->first()->toArray();
        $sales_associate["sales"] = Order::
                    whereIn('order_state', [3, 4, 5, 6])
                    ->where('order_scoupon', substr($sales_associate["id_file"], 0, 24))
                    ->sum('order_subtotal');

        $dashboard["transactions"] = AccountTransaction::where([
            ['trans_debit_account', "=", Auth::guard('sales-associate')->user()->id]
        ])->get()->toArray();

        return view('portal.main.sales-associate.dashboard')
            ->with("dashboard", $dashboard)
            ->with("sales_associate", $sales_associate);
    }

    public function showTermsOfUse(){
        return view("portal.main.sales-associate.terms-of-use");
    }
}
