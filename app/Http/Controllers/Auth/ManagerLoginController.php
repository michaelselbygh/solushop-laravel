<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Contracts\Activity; 
use Auth;

use App\Manager;

class ManagerLoginController extends Controller
{
    protected $redirectTo = '/portal/manager';

    public function __construct()
    {
        $this->middleware('guest:manager')->except('logout');
    }

    public function showLoginForm()
    {
        return view('portal.main.manager.login');
    }

    public function login(Request $request)
    {
        /*--- Validate form data  ---*/
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //attempt to log user in
        if(Auth::guard('manager')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)){
            
            
            //if successful, then redirect to intended location
            /*--- log activity ---*/
            activity()
            ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
            ->tap(function(Activity $activity) {
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Manager Login';
            })
            ->log(Auth::guard('manager')->user()->email.' logged in as a manager');
            
            return redirect()->intended(route('manager.dashboard'));
        }

        /*--- log activity ---*/
        activity()
        ->tap(function(Activity $activity) {
           $activity->causer_type = 'App\Manager';
           $activity->causer_id = '-';
           $activity->subject_type = 'System';
           $activity->subject_id = '0';
           $activity->log_name = 'Manager Login Attempt';
        })
        ->log($request->email.' attempted to log in as a manager');
        
        //if unsuccessful then redirect back to login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'))->with("error_message", "Invalid login credentials");
    }

    public function logout(){
        /*--- log activity ---*/
        activity()
        ->causedBy(Manager::where('id', Auth::guard('manager')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Manager Logout';
        })
        ->log(Auth::guard('manager')->user()->email.' logged out as a manager');

        Auth::guard('manager')->logout();
        
        return redirect(route('manager.login'));
    }
}
