<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

use App\ActivityLog;
use App\SalesAssociate;
use App\SMS;



class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:manager');
    }

    public function index()
    {
        return view('portal.main.manager.dashboard');
    }

    public function showActivityLog()
    {
        return view('portal.main.manager.activity-log')
                ->with('activity', ActivityLog::all()->toArray());
    }

    public function showSMSReport()
    {
        return view('portal.main.manager.sms-report')
                ->with('sms', SMS::with('state')->get()->toArray());
    }

    public function showSubscriptions()
    {
        return view('portal.main.manager.subscriptions')
                ->with('subscriptions', DB::select(
                    "SELECT *, vendor_subscriptions.id as subscription_id, vendor_subscriptions.created_at as subscription_created_at, vendor_subscriptions.updated_at as subscription_updated_at FROM vendors, vendor_subscriptions, vs_packages WHERE vendors.id = vendor_subscriptions.vs_vendor_id AND vendor_subscriptions.vs_vsp_id = vs_packages.id"
                ));
    }

    public function showSalesAssociates(){
        return view('portal.main.manager.sales-associates')
                ->with('sales_associates',  SalesAssociate::all()->toArray());
    }

    public function showAddSalesAssociate(){
        
    }

    public function processAddSalesAssociate(){
        
    }

    public function showSalesAssociate(){
        
    }

    public function processSalesAssociate(){
        
    }
}
