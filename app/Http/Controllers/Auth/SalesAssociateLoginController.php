<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class SalesAssociateLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:sales-associate')->except('logout');
    }

    public function showLoginForm()
    {
        return view('portal.main.sales-associate.login');
    }

    public function login(Request $request)
    {
        //validate form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //attempt to log user in
        if(Auth::guard('sales-associate')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)){
            
            //if successful, then redirect to intended location
            return redirect()->intended(route('sales-associate.dashboard'));
        }
        
        //if unsuccessful then redirect back to login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function logout(){
        Auth::guard('sales-associate')->logout();
        return redirect(route('sales-associate.login'));
    }
}
