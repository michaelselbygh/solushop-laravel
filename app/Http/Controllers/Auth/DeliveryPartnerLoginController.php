<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class DeliveryPartnerLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:delivery-partner')->except('logout');
    }

    public function showLoginForm()
    {
        return view('portal.main.delivery-partner.login');
    }

    public function login(Request $request)
    {
        /*--- Validate form data  ---*/
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //attempt to log user in
        if(Auth::guard('delivery-partner')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)){
            
            //if successful, then redirect to intended location
            return redirect()->intended(route('delivery-partner.dashboard'));
        }
        
        //if unsuccessful then redirect back to login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function logout(){
        Auth::guard('delivery-partner')->logout();
        return redirect(route('delivery-partner.login'));
    }
}
