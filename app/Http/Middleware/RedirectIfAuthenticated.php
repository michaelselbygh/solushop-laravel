<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        switch ($guard) {
            case 'manager':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('manager.dashboard');
                }
                break;

            case 'vendor':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('vendor.dashboard');
                }
                break;

            case 'sales-associate':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('sales-associate.dashboard');
                }
                break;

            case 'delivery-partner':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('delivery-partner.dashboard');
                }
                break;
            
            default:
                if (Auth::guard($guard)->check()) {
                    return redirect('/home');
                }
                break;
        }
        

        return $next($request);
    }
}
