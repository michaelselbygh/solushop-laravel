<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Contracts\Activity;

use Auth;
use Mail;

use App\Mail\Alert;

use App\AccountTransaction;
use App\Chocolate;
use App\Count;
use App\Customer;
use App\CustomerAddress;
use App\Milk;
use App\Order;
use App\OrderItem;
use App\Product;
use App\SalesAssociate;
use App\ShippingFare;
use App\SMS;
use App\StockKeepingUnit;

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

    public function showCustomers(){
        $management = [2, 5, 10];
        if(in_array(Auth::guard('sales-associate')->user()->id, $management)){
            $customers = Customer::with('milk', 'chocolate')->get()->toArray();
        }else{
            $customers = Customer::
            where('sm', Auth::guard('sales-associate')->user()->id)
            ->with('milk', 'chocolate')->get()->toArray();
        }
        return view('portal.main.sales-associate.customers')
                ->with('customers',  $customers);
    }

    public function showAddCustomer(){
        return view("portal.main.sales-associate.add-customer");
    }

    public function processAddCustomer(Request $request){
       /*--- Validate form data  ---*/
       $validator = Validator::make($request->all(), [
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required|digits:10',
        'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $errorMessageType = "error_message";
            $errorMessageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $errorMessageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput($request->only('email', 'phone', 'first_name', 'last_name'))->with($errorMessageType, $errorMessageContent);
        }



        //check for email existence in system
        if (Customer::where('email', $request->email)->first()) {
            return redirect()->back()->withInput($request->only('email', 'phone', 'first_name', 'last_name'))->with('error_message', 'Email in use by another account.');
        }
        //check for phone existence in system
        if (Customer::where('phone', "233".substr($request->phone, 1))->first()) {
            return redirect()->back()->withInput($request->only('email', 'phone', 'first_name', 'last_name'))->with('error_message', 'Phone number in use by another account.');
        }

        //hash password
        $password = rand(1000, 9999);
        $passwordHashed = bcrypt($password);

        //generate activation code
        $activationCode = mt_rand(1000, 9999);

        //generating customer ID part 1
        $customerID     = "C" . date('d') . date('m') . date('Y');

        //generating customer ID part 2
        $count = Count::first();
        $customerID .= substr("0000".$count->customer_count, strlen(strval($count->customer_count)));
        
        //account balance calculation
        $exp       = rand(1, 5);
        $milk      = pow(10, $exp);
        $chocolate = rand(1, 100);
        $milkshake = (5 + $chocolate) / $milk;

        //add customer
        $customer = new Customer;
        $customer->id = $customerID;
        $customer->first_name = ucwords(strtolower($request->first_name));
        $customer->last_name = ucwords(strtolower($request->last_name));
        $customer->email = strtolower($request->email);
        $customer->phone = "233".substr($request->phone, 1);
        $customer->password = $passwordHashed;
        $customer->activation_code = $activationCode;
        $customer->milkshake = $milkshake;
        $customer->sm = Auth::guard('sales-associate')->user()->id;
        $customer->save();

        //add milk
        $milkObject = new Milk;
        $milkObject->id = $customerID;
        $milkObject->milk_value = $milk;
        $milkObject->save();

        //add chocolate
        $chocolateObject = new Chocolate;
        $chocolateObject->id = $customerID;
        $chocolateObject->chocolate_value = $chocolate;
        $chocolateObject->save();

        //update count
        $count->customer_count++;
        $count->save();

        //queue customer message
        $sms_message = "Hi ".ucwords(strtolower($request->first_name)).", a warm welcome to the Solushop family. Your S-Wallet has been credited with GHS 5 bonus. If you need any assistance, kindly call or Whatsapp customer care on 0506753093 or your sales associate, ".Auth::guard('sales-associate')->user()->first_name.". Happy Shopping!\n\nEmail: ".strtolower($request->email)." \nPassword: $password";
        $sms_phone = "233".substr($request->phone, 1);

        $sms = new SMS;
        $sms->sms_message = $sms_message;
        $sms->sms_phone = $sms_phone;
        $sms->sms_state = 1;
        $sms->save();

        $data = array(
            'subject' => 'Welcome - Solushop Ghana',
            'name' => ucwords(strtolower($request->first_name)),
            'message' => "A warm welcome to the Solushop family. Your S-Wallet has been credited with GHS 5 bonus. If you need any assistance, kindly call or Whatsapp customer care on 0506753093 or your sales associate, ".Auth::guard('sales-associate')->user()->first_name.". Happy Shopping!<br><br>Email: ".strtolower($request->email)." <br>Password: $password"
        );

        Mail::to(strtolower($request->email), ucwords(strtolower($request->first_name)))
            ->queue(new Alert($data));

        /*--- log activity ---*/
        activity()
        ->causedBy(SalesAssociate::where('id', Auth::guard('sales-associate')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Customer Registration by Sales Associate';
        })
        ->log(strtolower($request->email).' was registered as a customer by '.Auth::guard('sales-associate')->user()->first_name." ".Auth::guard('sales-associate')->user()->last_name);

        return redirect()->back()->with("success_message", "Customer registered successfully");
    }

    public function showOrders(){
        $orders['all_orders'] = Order::
            orderBy("order_date")
            ->where('order_scoupon', substr(Auth::guard('sales-associate')->user()->id_file, 0, 24))
            ->with('order_items.sku.product.images', 'customer', 'order_state')
            ->get()
            ->toArray();

        return view('portal.main.sales-associate.orders')
            ->with('orders', $orders);
    }

    public function showOrder($orderID){
        if (is_null(Order::
            where("id", $orderID)
            ->with('order_items.sku.product.images', 'customer', 'order_state')
            ->first()
            ->toArray())) {
            
            return redirect()->route("sales-associate.show.orders")->with("error_message", "Order $orderID not found");
        }

        $order =  Order::
            where("id", $orderID)
            ->with('order_items.sku.product.images', 'order_items.sku.product.vendor', 'order_items.order_item_state', 'customer', 'order_state', 'address', 'coupon.sales_associate.badge_info')
            ->first()
            ->toArray();

        return view('portal.main.sales-associate.view-order')
                    ->with('order',$order);
    }


    public function showAddOrderOne(){
        $management = [2, 5, 10];
        if(in_array(Auth::guard('sales-associate')->user()->id, $management)){
            $customers = Customer::with('milk', 'chocolate')->get()->toArray();
        }else{
            $customers = Customer::
            where('sm', Auth::guard('sales-associate')->user()->id)
            ->with('milk', 'chocolate')->get()->toArray();
        }
        return view('portal.main.sales-associate.add-order-customer')
                ->with('customers',  $customers);
    }

    public function showAddOrderTwo($customerID){
        if (is_null(Customer::
            where("id", $customerID)
            ->first())) {
            
            return redirect()->route("sales-associate.show.orders")->with("error_message", "Customer $customerID not found");
        }
            
        $customer_addresses["customer"] = Customer::where("id", $customerID)->first()->toArray();
        $customer_addresses["records"] = CustomerAddress::where('ca_customer_id', $customerID)->get()->toArray();
        $customer_addresses["options"] = ShippingFare::orderBy("sf_town")->get()->toArray();

        return view('portal.main.sales-associate.add-order-address')
                ->with('customer_addresses',  $customer_addresses);
    }

    function processAddOrderTwo(Request $request, $customerID){
        $validator = Validator::make($request->all(), [
            'address_town' => 'required',
            'address_details' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessageType = "error_message";
            $errorMessageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $errorMessageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput($request->only('address_town', 'address_details'))->with($errorMessageType, $errorMessageContent);
        }

        //add address
        $address = new CustomerAddress;
        $ca_town_region = explode("||", $request->address_town);
        $address->ca_customer_id    = $customerID;
        $address->ca_region         = $ca_town_region[1]." Region";
        $address->ca_town           = $ca_town_region[0];
        $address->ca_address        = $request->address_details;
        $address->save();

        /*--- log activity ---*/
        activity()
        ->causedBy(SalesAssociate::where('id', Auth::guard('sales-associate')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Address Address';
        })
        ->log(Auth::guard('sales-associate')->user()->first_name." ".Auth::guard('sales-associate')->user()->last_name.' added an address for customer with ID '.$customerID);


        return redirect()->back()->with("success_message", "Address added successfully.");

    }

    public function showAddOrderThree($customerID, $addressID){
        if (is_null(CustomerAddress::
            where("id", $addressID)
            ->where("ca_customer_id", $customerID)
            ->first())) {
            
            return redirect()->route("sales-associate.show.add.order.step-1", $customerID)->with("error_message", "Address AD-$addressID not found");
        }
            
        $order["customer"] = Customer::where("id", $customerID)->first()->toArray();
        $order["address"] = CustomerAddress::where('id', $addressID)->with("shipping_fare")->first()->toArray();
        $order["products"] = Product::where('product_state', 1)->with("images", "vendor", "skus")->get()->toArray();

        return view('portal.main.sales-associate.add-order-products')
                ->with('order',  $order);
    }

    public function processAddOrder($customerID, $addressID, Request $request){
        if (is_null(CustomerAddress::
            where("id", $addressID)
            ->where("ca_customer_id", $customerID)
            ->first())) {
            
            return redirect()->route("sales-associate.show.add.order.step-1", $customerID)->with("error_message", "Address AD-$addressID not found");
        }

        if ($request->orderItemsCount < 1) {
            return redirect()->back()->with("error_message", "Please select at least one product");
        }

        $customer = Customer::where("id", $customerID)->first();
        $address = CustomerAddress::where('id', $addressID)->with("shipping_fare")->first()->toArray();

        $shipping = $request->orderShipping;
        $order_item_skus = explode(",", $request->orderItemsSKU);
        $order_item_quantities = explode(",", $request->orderItemsQuantity);
        $order_item_subtotal = $request->orderItemSubTotal;

        $count = Count::first();
        $count->order_count++;
        $order_id = "OD".date('Ymd').substr("0000".$count->order_count, strlen(strval($count->order_count)));

        //insert order
        $order = new Order;
        $order->id = $order_id;
        $order->order_type = 0;
        $order->order_customer_id = $customerID;
        $order->order_address_id = $addressID;
        $order->order_subtotal = $order_item_subtotal;
        $order->order_shipping = $shipping;

        $order->order_token = NULL;
        $order->order_scoupon = substr(Auth::guard('sales-associate')->user()->id_file, 0, 24);
        $order->order_state = 1;
        $order->order_date = date('Y-m-d H:i:s');

        $order->save();
        $count->save();

        //inserting order items
        for ($i=0; $i < sizeof($order_item_skus); $i++) {
            $order_item_sku = StockKeepingUnit::where('id', $order_item_skus[$i])->with("product")->first();
            $order_item = new OrderItem;
            $order_item->oi_order_id            = $order_id;
            $order_item->oi_sku                 = $order_item_skus[$i];
            $order_item->oi_name                = $order_item_sku->product->product_name;
            if (trim(strtolower($order_item_sku->sku_variant_description)) != "none") {
                $order_item->oi_name .= " - ".$order_item_sku->sku_variant_description;
            }
            $order_item->oi_selling_price       = $order_item_sku->sku_selling_price;
            $order_item->oi_settlement_price    = $order_item_sku->sku_settlement_price;
            $order_item->oi_discount            = $order_item_sku->sku_discount;
            $order_item->oi_quantity            = $order_item_quantities[$i];
            $order_item->oi_state               = 1;
            $order_item->save();
        }

        /*--- Notify customer ---*/
        $sms_message = "Hi ".$customer->first_name.", kindly pay GHS ".($shipping + (0.99 * $order_item_subtotal))." to Solushop Ghana Official Number - 0506753093  (VF Cash - Solushop Ghana Limited) to confirm your order $order_id. Alert your sales associate immediately you do.";
        $sms_phone = $customer->phone;

        $sms = new SMS;
        $sms->sms_message = $sms_message;
        $sms->sms_phone = $sms_phone;
        $sms->sms_state = 1;
        $sms->save();

        $data = array(
            'subject' => 'Payment Instructions - Solushop Ghana',
            'name' => $customer->first_name,
            'message' => "Kindly pay GHS ".($shipping + (0.99 * $order_item_subtotal))." to Solushop Ghana Official Number - 0506753093  (VF Cash - Solushop Ghana Limited) to confirm your order $order_id. Alert your sales associate immediately you do."
        );

        Mail::to($customer->email, $customer->first_name)
            ->queue(new Alert($data));

        /*--- log activity ---*/
        activity()
        ->causedBy(SalesAssociate::where('id', Auth::guard('sales-associate')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Order Initiated by Sales Associate';
        })
        ->log(Auth::guard('sales-associate')->user()->first_name." ".Auth::guard('sales-associate')->user()->last_name.' added an order '.$order_id.' for customer with ID '.$customerID);


        return redirect()->route("sales-associate.show.orders")->with("success_message", $customer->first_name."'s order placed successfully.");

    }
    
    public function showTermsOfUse(){
        return view("portal.main.sales-associate.terms-of-use");
    }
}
