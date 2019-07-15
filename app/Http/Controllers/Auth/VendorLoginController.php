<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class VendorLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:vendor')->except('logout');
    }

    public function showLoginForm()
    {
        return view('portal.main.vendor.login');
    }

    public function login(Request $request)
    {
        //validate form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //attempt to log user in
        if(Auth::guard('vendor')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)){
            
            //if successful, then redirect to intended location
            return redirect()->intended(route('vendor.dashboard'));
        }
        
        //if unsuccessful then redirect back to login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function logout(){
        Auth::guard('vendor')->logout();
        return redirect(route('vendor.login'));
    }
}
