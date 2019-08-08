<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;
use PDF;

use App\AccountTransaction;
use App\Customer;
use App\DeliveredItem;
use App\DeliveryPartner;
use App\Order;
use App\OrderItem;
use App\PickedUpItem;
use App\SMS;
use App\Vendor;

class DeliveryPartnerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:delivery-partner');
    }

    public function index()
    {
        $dashboard["transactions"] = AccountTransaction::where([
            ['trans_debit_account', "=", Auth::guard('delivery-partner')->user()->id]
        ])->get()->toArray();

        return view('portal.main.delivery-partner.dashboard')
            ->with("dashboard", $dashboard);
    }

    public function showPickups(){
        return view('portal.main.delivery-partner.pick-ups')
                ->with('pick_up_items',  OrderItem::whereIn('oi_state', [2])->with("sku.product.images", "sku.product.vendor")->get()->toArray());
    }

    public function processPickups(Request $request){
        switch ($request->pick_up_action) {
            case 'mark_item':
                /*--- Change order Item State ---*/
                OrderItem::
                    where([
                        ['id', '=', $request->picked_up_item_id]
                    ])
                    ->update([
                        'oi_state' => 3,
                    ]);
                
                $order_item = OrderItem::where('id', $request->picked_up_item_id)->first()->toArray();
                $order = Order::where('id', $order_item['oi_order_id'])->with('customer')->first()->toArray();

                /*--- Change Order State (where necessary) ---*/
                $order_items_count = OrderItem::where('oi_order_id', $order_item['oi_order_id'])->get()->count();
                $picked_up_order_items_count = OrderItem::where('oi_order_id', $order_item['oi_order_id'])->whereIn('oi_state', [3, 4])->get()->count();
                

                if ($order_items_count == $picked_up_order_items_count) {
                    Order::
                    where([
                        ['id', '=', $order_item['oi_order_id']]
                    ])
                    ->update([
                        'order_state' => 4,
                    ]);
                }

                /*--- Notify Customer ---*/
                $sms = new SMS;
                $sms->sms_message = "Hi ".$order["customer"]["first_name"]." your ordered item, ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." has been picked up and is ready for delivery.";
                $sms->sms_phone = $order["customer"]["phone"];
                $sms->sms_state = 1;
                $sms->save();

                /*--- Record Pickup History ---*/
                $picked_up_item = new PickedUpItem;
                $picked_up_item->pui_order_item_id          = $order_item["id"];
                $picked_up_item->pui_marked_by_id           = Auth::guard('delivery-partner')->user()->id;
                $picked_up_item->pui_marked_by_description  = Auth::guard('delivery-partner')->user()->first_name." ".Auth::guard('delivery-partner')->user()->last_name;
                $picked_up_item->save();

                /*--- Log Activity ---*/
                activity()
                ->causedBy(DeliveryPartner::where('id', Auth::guard('delivery-partner')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Item Picked Up';
                })
                ->log(Auth::guard('delivery-partner')->user()->email." marked ordered item [ ".$order_item["id"]." ] ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." as picked up.");

                /*--- Return with success message ---*/
                return redirect()->back()->with("success_message", $order_item["oi_quantity"]." ".$order_item["oi_name"]." marked as picked up successfully.");
                break;

            case 'download_pick_up_guide':
                //get order items information
                $data["pick_ups"] = OrderItem::orderBy('oi_name', 'asc')->where('oi_state', 2)->with('sku.product.vendor')->get()->toArray();

                //get vendor information
                $data["vendors"] =  DB::select(
                    "SELECT distinct vendors.id, vendors.phone, vendors.alt_phone, vendors.name, vendors.address from vendors, order_items, stock_keeping_units, products where oi_state = '2' and order_items.oi_sku = stock_keeping_units.id and stock_keeping_units.sku_product_id = products.id and products.product_vid = vendors.id order by vendors.name"
                );

                /*--- Log Activity ---*/
                activity()
                ->causedBy(DeliveryPartner::where('id', Auth::guard('delivery-partner')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Pick-Up Guide Download';
                })
                ->log(Auth::guard('delivery-partner')->user()->email." downloaded Pick-Up Guide ".date('m-d-Y').".pdf");

                $pdf = PDF::loadView('portal.guides.pick-up', array('data' => $data));
                return $pdf->download('Pick-Up Guide '.date('m-d-Y').'.pdf');

                break;
            
            default:
                return redirect()->back()->with("error_message", "Something went wrong, please try again.");
                break;
        }
    }

    public function showDeliveries(){
        return view('portal.main.delivery-partner.deliveries')
                ->with('delivery_items',  OrderItem::whereIn('oi_state', [2, 3])->with("sku.product.images")->get()->toArray());
    }

    public function processDeliveries(Request $request){
        switch ($request->delivery_action) {
            case 'mark_item':
                /*--- Change order Item State ---*/
                OrderItem::
                    where([
                        ['id', '=', $request->delivered_item_id]
                    ])
                    ->update([
                        'oi_state' => 4,
                    ]);
                
                $order_item = OrderItem::where('id', $request->delivered_item_id)->with('sku.product.vendor')->first()->toArray();
                $order = Order::where('id', $order_item['oi_order_id'])->with('customer')->first()->toArray();

                /*--- Change Order State (where necessary) ---*/
                $order_items_count = OrderItem::where('oi_order_id', $order_item['oi_order_id'])->get()->count();
                $delivered_order_items_count = OrderItem::where('oi_order_id', $order_item['oi_order_id'])->whereIn('oi_state', [4])->get()->count();
                

                if ($order_items_count == $delivered_order_items_count) {
                    Order::
                    where([
                        ['id', '=', $order_item['oi_order_id']]
                    ])
                    ->update([
                        'order_state' => 6,
                    ]);
                }

                /*--- Notify Customer ---*/
                $sms = new SMS;
                $sms->sms_message = "Hi ".$order["customer"]["first_name"]." your ordered item, ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." has been delivered successfully. Thanks, come back soon.";
                $sms->sms_phone = $order["customer"]["phone"];
                $sms->sms_state = 1;
                $sms->save();

                /*--- Accrue to Vendor || Record Transaction ---*/
                $vendor = Vendor::where('id', $order_item['sku']["product"]["vendor"]["id"])->first();
                $vendor->balance += round(($order_item['oi_settlement_price'] - $order_item['oi_discount']) * $order_item['oi_quantity'], 2);

                $transaction = new AccountTransaction;
                $transaction->trans_type                = "Vendor Accrual";
                $transaction->trans_amount              = round(($order_item['oi_settlement_price'] - $order_item['oi_discount']) * $order_item['oi_quantity'], 2);
                $transaction->trans_credit_account_type = 1;
                $transaction->trans_credit_account      = "INT-SC001";
                $transaction->trans_debit_account_type  = 3;
                $transaction->trans_debit_account       = $vendor->id;
                $transaction->trans_description         = $log = "Accrual of GH¢ ".round(($order_item['oi_settlement_price'] - $order_item['oi_discount']) * $order_item['oi_quantity'], 2)." to ".$vendor->name." for ordered item [ ".$order_item["id"]." ] ".$order_item["oi_quantity"]." ".$order_item["oi_name"];
                $transaction->trans_date                = date("Y-m-d G:i:s");
                $transaction->trans_recorder            = Auth::guard('delivery-partner')->user()->first_name." ".Auth::guard('delivery-partner')->user()->last_name;
                $transaction->save();


                //record transaction
                $vendor->save();

                /*--- Record Delivery History ---*/
                $delivered_item = new DeliveredItem;
                $delivered_item->di_order_item_id          = $order_item["id"];
                $delivered_item->di_marked_by_id           = Auth::guard('delivery-partner')->user()->id;
                $delivered_item->di_marked_by_description  = Auth::guard('delivery-partner')->user()->first_name." ".Auth::guard('delivery-partner')->user()->last_name;
                $delivered_item->save();

                /*--- Log Activity ---*/
                activity()
                ->causedBy(DeliveryPartner::where('id', Auth::guard('delivery-partner')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Order Item Delivered';
                })
                ->log(Auth::guard('delivery-partner')->user()->email." marked ordered item [ ".$order_item["id"]." ] ".$order_item["oi_quantity"]." ".$order_item["oi_name"]." as delivered.");

                /*--- Return with success message ---*/
                return redirect()->back()->with("success_message", $order_item["oi_quantity"]." ".$order_item["oi_name"]." marked as delivered successfully.");
                break;

            case 'download_delivery_guide':
                //get order items information
                $data["deliveries"] = OrderItem::orderBy('oi_name', 'asc')->whereIn('oi_state', [2, 3])->with('order.customer')->get()->toArray();

                //get customers information
                $data["customers"] =  DB::select(
                    "SELECT distinct customers.id, customers.phone, customer_addresses.ca_town, customer_addresses.ca_address, customers.first_name, customers.last_name from customers, customer_addresses, order_items, orders where (oi_state = '2' OR oi_state = '3') and order_items.oi_order_id = orders.id and orders.order_customer_id = customers.id and orders.order_address_id = customer_addresses.id order by customers.first_name"
                );

                /*--- Log Activity ---*/
                activity()
                ->causedBy(DeliveryPartner::where('id', Auth::guard('delivery-partner')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Delivery Guide Download';
                })
                ->log(Auth::guard('delivery-partner')->user()->email." downloaded Delivery Guide ".date('m-d-Y').".pdf");

                $pdf = PDF::loadView('portal.guides.delivery', array('data' => $data));
                return $pdf->download('Delivery Guide '.date('m-d-Y').'.pdf');
                break;
            
            default:
                return redirect()->back()->with("error_message", "Something went wrong, please try again.");
                break;
        }
    }
}
