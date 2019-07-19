<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\ProductCategory;

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
        $productCategories = ProductCategory::
                            where('pc_level', 2)    
                            ->get();
        return view('app.main.general.login')
                ->with('productCategories', $productCategories);
    }

    //overriding the login function
    public function login(Request $request)
    {
        //check if login or register button was clicked
        if($request->solushop_lr == 'login'){
           //validate form data
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required'
            ]);


            //attempt to log user in
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                
                //if successful, then redirect to intended location
                return redirect()->intended(route('home'));
            }
            
            //if unsuccessful then redirect back to login with the form data
            return redirect()->back()->withInput($request->only('email'))->with('login_error_message', 'Invalid login credentials.');
        }
        
    }

    public function logout(){
        Auth::guard('web')->logout();
        return redirect(route('welcome'));
    }
}
