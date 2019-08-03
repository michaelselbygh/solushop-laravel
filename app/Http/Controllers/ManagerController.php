<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

use App\ActivityLog;
use App\Order;
use App\SalesAssociate;
use App\SMS;



class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:manager');
    }

    public function showCustomers(){

    }

    public function showCustomer(){

    }

    public function processCustomer(){

    }

    public function showOrders(){

    }

    public function processOrders(){

    }

    public function showOrder(){

    }

    public function processOrder(){

    }

    public function showMessages(){

    }

    public function showFlaggedMessages(){

    }

    public function showDeletedMessages(){

    }

    public function showConversation(){

    }

    public function processConversation(){

    }

    public function showProducts(){

    }

    public function showDeletedProducts(){

    }

    public function showAddProduct(){

    }

    public function processAddProduct(){

    }

    public function showProduct(){

    }

    public function processProduct(){

    }

    public function showVendors(){

    }

    public function showAddVendor(){

    }

    public function processAddVendor(){

    }

    public function showVendor(){

    }

    public function processVendor(){

    }

    public function showPickupHistory(){

    }

    public function showActivePickups(){

    }

    public function processActivePickups(){

    }

    public function showDeliveryHistory(){

    }

    public function showActiveDeliveries(){

    }

    public function processActiveDeliveries(){

    }

    public function showCoupons(){

    }

    public function showGenerateCoupon(){

    }

    public function processGenerateCoupon(){
        
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

    public function showAccounts(){
        
    }

    public function processAccounts(){
        
    }

    public function processSubscriptions(Request $request)
    {
        return view('portal.main.manager.subscriptions')
                ->with('subscriptions', DB::select(
                    "SELECT *, vendor_subscriptions.id as subscription_id, vendor_subscriptions.created_at as subscription_created_at, vendor_subscriptions.updated_at as subscription_updated_at FROM vendors, vendor_subscriptions, vs_packages WHERE vendors.id = vendor_subscriptions.vs_vendor_id AND vendor_subscriptions.vs_vsp_id = vs_packages.id"
                ));
    }

    public function showSubscriptions()
    {
        return view('portal.main.manager.subscriptions')
                ->with('subscriptions', DB::select(
                    "SELECT *, vendor_subscriptions.id as subscription_id, vendor_subscriptions.created_at as subscription_created_at, vendor_subscriptions.updated_at as subscription_updated_at FROM vendors, vendor_subscriptions, vs_packages WHERE vendors.id = vendor_subscriptions.vs_vendor_id AND vendor_subscriptions.vs_vsp_id = vs_packages.id"
                ));
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

    public function index()
    {
        //get counts
        $dashboard['new_orders_count'] = count(Order::
                where('order_state', 2)
                ->get()
            );

        $dashboard['ongoing_orders_count'] = count(Order::
                whereIn('order_state', [3, 4, 5])
                ->get()
            );
        
        $dashboard['completed_orders_count'] = count(Order::
                where('order_state', 6)
                ->get()
            );

        $dashboard['cancelled_orders_count'] = count(Order::
            where('order_state', 7)
            ->get()
        );

        $dashboard['total_orders_count'] = count(Order::
            whereIn('order_state', [2, 3, 4, 5, 6, 7])
            ->get()
        );

        if($dashboard['new_orders_count'] > 0){
            //get new orders
        }

        return view('portal.main.manager.dashboard')
            ->with('dashboard', $dashboard);
    }
}
