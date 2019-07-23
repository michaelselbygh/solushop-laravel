<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

use App\Customer;
use App\Count;
use App\Chocolate;
use App\Milk;
use App\Manager;
use App\ProductCategory;
use App\SMS;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {   
        $search_bar_pc_options = ProductCategory::
                            where('pc_level', 2) 
                            ->orderBy('pc_description')     
                            ->get(['id', 'pc_description']);
        return view('app.main.general.login')
                ->with('search_bar_pc_options', $search_bar_pc_options);
    }

    //overriding the default login function
    public function login(Request $request)
    {
        //check if login or register button was clicked
        if(isset($request->login)){
            //validate form data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput($request->only('email'))->with('login_error_message', 'Invalid login credentials.');
            }

            //attempt to log user in
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                
                //if successful, then redirect to intended location
                return redirect()->intended(route('home'));
            }
            
            //if unsuccessful then redirect back to login with the form data
            return redirect()->back()->withInput($request->only('email'))->with('login_error_message', 'Invalid login credentials.');
        }elseif(isset($request->register)){
            //validate form data
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
            $milkshake = (5 + rand(1, 100)) / pow(10, $exp);

            //add customer
            $customer = new Customer;
            $customer->id = $customerID;
            $customer->first_name = ucwords(strtolower($request->first_name));
            $customer->last_name = ucwords(strtolower($request->last_name));
            $customer->email = $request->r_email;
            $customer->phone = "233".substr($request->phone, 1);
            $customer->password = $passwordHashed;
            $customer->activation_code = $activationCode;
            $customer->milkshake = $milkshake;
            $customer->save();

            //add milk
            $milkObject = new Milk;
            $milkObject->milk_customer_id = $customerID;
            $milkObject->milk_value = $milk;
            $milkObject->save();

            //add chocolate
            $chocolateObject = new Chocolate;
            $chocolateObject->chocolate_customer_id = $customerID;
            $chocolateObject->chocolate_value = $chocolate;
            $chocolateObject->save();

            //update count
            $count->customer_count++;
            $count->save();

            //queue customer message
            $sms_message = "Hi ".ucwords(strtolower($request->first_name)).", a warm welcome to the Solushop family. Your Solushop Wallet has been credited with 5 cedis as your signup bonus. If you need any assistance, kindly call or whatsapp customer care on 0506753093. Happy Shopping!";
            $sms_phone = "233".substr($request->phone, 1);

            $sms = new SMS;
            $sms->sms_message = $sms_message;
            $sms->sms_phone = $sms_phone;
            $sms->sms_state = 1;
            $sms->save();

            //queue sign up message for managers
            $customerCount = Customer::count();

            $managers = Manager::where('sms', 0)->get();
            foreach ($managers as $manager) {
                $sms = new SMS;
                $sms->sms_message = "Customer Sign-Up - ".ucwords(strtolower($request->first_name))." ".ucwords(strtolower($request->last_name)).". \nTotal sign-ups now is $customerCount";
                $sms->sms_phone = $manager->phone;
                $sms->sms_state = 1;
                $sms->save();
            }

            Auth::loginUsingId($customerID);
            return redirect()->intended(route('home'));
        }
        
    }

    public function showResetPasswordForm()
    {   
        $search_bar_pc_options = ProductCategory::
                            where('pc_level', 2) 
                            ->orderBy('pc_description')     
                            ->get(['id', 'pc_description']);
        return view('app.main.general.reset-password')
                ->with('search_bar_pc_options', $search_bar_pc_options);
    }

    public function resetPassword(Request $request)
    {
        //validate form data
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

            //set success message
            $messageType = 'success_message';
            $messageContent = 'Password reset successfully. Please give up to a minute to receive the sms.';


        }else{
            //set error message
            $messageType = 'error_message';
            $messageContent = 'Sorry, no matching records found.';
        }

        
        //if unsuccessful then redirect back to login with the form data
        return redirect()->back()->withInput($request->only('email', 'phone'))->with($messageType, $messageContent);
    
        
    }


    public function logout(){
        Auth::guard('web')->logout();
        return redirect(route('home'));
    }
}
