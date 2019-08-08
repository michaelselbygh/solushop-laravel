<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Contracts\Activity; 
use Auth;

use App\DeliveryPartner;

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
            /*--- log activity ---*/
            activity()
            ->causedBy(DeliveryPartner::where('id', Auth::guard('delivery-partner')->user()->id)->get()->first())
            ->tap(function(Activity $activity) {
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Delivery Partner Login';
            })
            ->log(Auth::guard('delivery-partner')->user()->email.' logged in as a delivery partner');


            return redirect()->intended(route('delivery-partner.dashboard'));
        }
        
        /*--- log activity ---*/
        activity()
        ->tap(function(Activity $activity) {
            $activity->causer_type = 'App\DeliveryPartner';
            $activity->causer_id = '-';
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Delivery Partner Login Attempt';
        })
        ->log($request->email.' attempted to log in as a delivery partner');
        
         
        //if unsuccessful then redirect back to login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'))->with("error_message", "Invalid login credentials");
    }

    public function logout(){
        /*--- log activity ---*/
        activity()
        ->causedBy(DeliveryPartner::where('id', Auth::guard('delivery-partner')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Delivery Partner Logout';
        })
        ->log(Auth::guard('delivery-partner')->user()->email.' logged out as a delivery partner');

        Auth::guard('delivery-partner')->logout();
        return redirect(route('delivery-partner.login'));
    }
}
