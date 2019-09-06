<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Spatie\Activitylog\Contracts\Activity;
use Auth;
use Mail;

use App\Mail\Alert;

use App\Customer;
use App\Count;
use App\Chocolate;
use App\Milk;
use App\Manager;
use App\ProductCategory;
use App\SMS;

use \Mobile_Detect;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {   
        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            return redirect()->route('show.shop');

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.login')
            ->with('customer_information', $customer_information);
        }else{
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')     
            ->get(['id', 'pc_description']);
            return view('app.main.general.login')
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('customer_information', $customer_information);
        }
    }

    public function showRegisterForm()
    {   

        /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            return redirect()->route('show.shop');

        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.register')
            ->with('customer_information', $customer_information);
        }else{
            $search_bar_pc_options = ProductCategory::
            where('pc_level', 2) 
            ->orderBy('pc_description')     
            ->get(['id', 'pc_description']);
            return view('app.main.general.login')
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('customer_information', $customer_information);
        }
        
    }

    //overriding the default login function
    public function login(Request $request)
    {
        //check if login or register button was clicked
        if(isset($request->login)){
            /*--- Validate form data  ---*/
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $url = explode($_SERVER['SERVER_NAME'], $request->url);

            if ($validator->fails()) {
                return redirect()->back()->withInput($request->only('email'))->with('login_error_message', 'Invalid login credentials.');
            }

            //attempt to log user in
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                
                //if successful, then redirect to intended location
                if(in_array($url[sizeof($url)-1], ['/login', '/register'])){
                    return redirect()->route('show.shop');
                }else{
                    if ($url[sizeof($url)-1] == "/" OR $url[sizeof($url)-1] == "") {
                        $return_url = "shop";
                    }else{
                        $return_url = substr($url[sizeof($url)-1], 1);
                    }
                    /*--- log activity ---*/
                    activity()
                    ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
                    ->tap(function(Activity $activity) {
                        $activity->subject_type = 'System';
                        $activity->subject_id = '0';
                        $activity->log_name = 'Customer Login';
                    })
                    ->log(Auth::user()->email.' logged in as a customer');
                    
                    return redirect($return_url)->with('welcome_message', 'Welcome back, '.Auth::user()->first_name.'.');
                }
                
            }
             /*--- log activity ---*/
             activity()
             ->tap(function(Activity $activity) {
                $activity->causer_type = 'App\Customer';
                $activity->causer_id = '-';
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Customer Login Attempt';
             })
             ->log($request->email.' attempted to log in as a customer');
            
            //if unsuccessful then redirect back to login with the form data
            return redirect()->back()->withInput($request->only('email'))->with('login_error_message', 'Invalid login credentials.');
        }elseif(isset($request->register)){
            /*--- Validate form data  ---*/
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required|digits:10',
                'r_email' => 'required|email',
                'r_password' => 'required'
            ]);

            if ($validator->fails()) {
                $errorMessageType = "register_error_message";
                $errorMessageContent = "";

                foreach ($validator->messages()->getMessages() as $field_name => $messages)
                {
                    $errorMessageContent .= $messages[0]." "; 
                }

                return redirect()->back()->withInput($request->only('r_email', 'phone', 'first_name', 'last_name'))->with($errorMessageType, $errorMessageContent);
            }



            //check for email existence in system
            if (Customer::where('email', $request->r_email)->first()) {
                return redirect()->back()->withInput($request->only('r_email', 'phone', 'first_name', 'last_name'))->with('register_error_message', 'Email in use by another account.');
            }
            //check for phone existence in system
            if (Customer::where('phone', "233".substr($request->phone, 1))->first()) {
                return redirect()->back()->withInput($request->only('r_email', 'phone', 'first_name', 'last_name'))->with('register_error_message', 'Phone number in use by another account.');
            }

            //hash password
            $passwordHashed = bcrypt($request->r_password);

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
            $customer->email = strtolower($request->r_email);
            $customer->phone = "233".substr($request->phone, 1);
            $customer->password = $passwordHashed;
            $customer->activation_code = $activationCode;
            $customer->milkshake = $milkshake;
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
            $sms_message = "Hi ".ucwords(strtolower($request->first_name)).", a warm welcome to the Solushop family. Your Solushop Wallet has been credited with GHS 5.00 as your signup bonus. If you need any assistance, kindly call or Whatsapp customer care on 0506753093. Happy Shopping!";
            $sms_phone = "233".substr($request->phone, 1);

            $sms = new SMS;
            $sms->sms_message = $sms_message;
            $sms->sms_phone = $sms_phone;
            $sms->sms_state = 1;
            $sms->save();

          
            //queue sign up message for managers
            $customerCount = Customer::count();

            // $managers = Manager::where('sms', 0)->get();
            // foreach ($managers as $manager) {
            //     $sms = new SMS;
            //     $sms->sms_message = "Customer Sign-Up - ".ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name)).". \nTotal sign-ups now is $customerCount";
            //     $sms->sms_phone = $manager->phone;
            //     $sms->sms_state = 1;
            //     $sms->save();
            // }

            Auth::loginUsingId($customerID);

            $data = array(
                'subject' => 'Welcome - Solushop Ghana',
                'name' => Auth::user()->first_name,
                'message' => "A warm welcome to the Solushop family. Your Solushop Wallet has been credited with GHS 5.00 as your signup bonus. If you need any assistance, kindly call or Whatsapp customer care on 0506753093. Happy Shopping!"
            );

            Mail::to(Auth::user()->email, Auth::user()->first_name)
                ->queue(new Alert($data));


            /*--- log activity ---*/
            activity()
            ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
            ->tap(function(Activity $activity) {
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Customer Sign Up';
            })
            ->log(Auth::user()->email.' registered as a customer');


            
            $url = explode($_SERVER['SERVER_NAME'], $request->url);

            if(in_array($url[sizeof($url)-1], ['/login', '/register'])){
                return redirect()->route('show.shop');
            }else{
                if ($url[sizeof($url)-1] == "/" OR $url[sizeof($url)-1] == "") {
                    $return_url = "shop";
                }else{
                    $return_url = substr($url[sizeof($url)-1], 1);
                }
                return redirect($return_url)->with('welcome_message', 'Welcome to Solushop, '.ucwords(strtolower($request->first_name)).'.');
            }
        }
        
    }

    public function showResetPasswordForm()
    {   /*---retrieving customer information if logged in---*/
        if (Auth::check()) {
            $customer_information_object = Customer::
                where('id', Auth::user()->id)
                ->with('milk', 'chocolate', 'cart', 'wishlist')
                ->first()
                ->toArray();

            //calculate account balance
            $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

            //get cart count
            $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

            //get wishlist count
            $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

            return redirect()->route('show.shop');
        }else{
            $customer_information['wallet_balance'] = 0;
            $customer_information['cart_count'] = 0;
            $customer_information['wishlist_count'] = 0;
        }

        $detect = new Mobile_Detect;
        if( $detect->isMobile() && !$detect->isTablet() ){
            return view('mobile.main.general.reset-password')
            ->with('customer_information', $customer_information);
        }else{
            $search_bar_pc_options = ProductCategory::
                where('pc_level', 2) 
                ->orderBy('pc_description')     
                ->get(['id', 'pc_description']);
            return view('app.main.general.reset-password')
                ->with('search_bar_pc_options', $search_bar_pc_options)
                ->with('customer_information', $customer_information);
        }
        
    }

    public function resetPassword(Request $request)
    {
        /*--- Validate form data  ---*/
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'phone' => 'required|digits:10'
        ]);

        if ($validator->fails()) {
            $messageType = "error_message";
            $messageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $messageContent .= $messages[0]." "; 
            }

            return redirect()->back()->withInput($request->only('email', 'phone'))->with($messageType, $messageContent);
        }

        //select matching record from database
        $customer =  Customer::
                        where([
                            ['email', '=', $request->email],
                            ['phone', '=', "233".substr($request->phone, 1)]
                        ])    
                        ->first();
        
        if($customer){
            //reset password
            
            //generate new password
            $newPassword = str_random(5);
            $newPasswordHashed = bcrypt($newPassword);

            //update customer record
            $customer->password = $newPasswordHashed;
            $customer->save();

            //queue sms to customer
            $sms_message = "Hi ".$customer->first_name.", your password has been reset.\nTemporary password - $newPassword \n\nHappy Shopping!";
            $sms_phone = $customer->phone;

            $sms = new SMS;
            $sms->sms_message = $sms_message;
            $sms->sms_phone = $sms_phone;
            $sms->sms_state = 1;
            $sms->save();

            $data = array(
                'subject' => 'Password Reset - Solushop Ghana',
                'name' => $customer->first_name,
                'message' => "Your password has been reset.<br>Temporary password - $newPassword <br><br>Happy Shopping!"
            );

            Mail::to($customer->email, $customer->first_name)
                ->queue(new Alert($data));

            /*--- log activity ---*/
            activity()
            ->causedBy(Customer::where('id', $customer->id)->get()->first())
            ->tap(function(Activity $activity) {
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Customer Password Reset';
            })
            ->log($request->email.' reset their account password with phone number '.$request->phone);

            //set success message
            $messageType = 'success_message';
            $messageContent = 'Password reset successfully.';


        }else{
            /*--- log activity ---*/
            activity()
            ->tap(function(Activity $activity) {
               $activity->causer_type = 'App\Customer';
               $activity->causer_id = '-';
               $activity->subject_type = 'System';
               $activity->subject_id = '0';
               $activity->log_name = 'Customer Password Reset Attempt';
            })
            ->log($request->email.' attempted to reset password with phone number '.$request->phone);

            //set error message
            $messageType = 'error_message';
            $messageContent = 'Sorry, no matching records found.';
        }

        
        //if unsuccessful then redirect back to login with the form data
        return redirect()->back()->withInput($request->only('email', 'phone'))->with($messageType, $messageContent);
    
        
    }


    public function logout(){
        /*--- log activity ---*/
        activity()
        ->causedBy(Customer::where('id', Auth::user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Customer Logout';
        })
        ->log(Auth::user()->email.' logged out as customer');

        Auth::guard('web')->logout();
        return redirect(route('home'));
    }
}
