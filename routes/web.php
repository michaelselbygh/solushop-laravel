<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');



Auth::routes();

//mobile app routes
Route::group(['domain' => 'm.solushop.laravel'], function () {
    Route::get('/', 'MobileAppHomeController@showHome')->name('mobile.home');
});

//desktop & tablet app routes
Route::get('/', 'AppHomeController@showHome')->name('home');
Route::get('/logout', 'Auth\LoginController@logout')->name('customer.logout');
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login')->name('customer.login.submit');
Route::get('/reset-password', 'Auth\LoginController@showResetPasswordForm')->name('customer.reset.password');
Route::post('/reset-password', 'Auth\LoginController@resetPassword')->name('customer.reset.password.submit');


//portal routes
Route::prefix('portal')->group(function(){
    //manager routes
    Route::prefix('manager')->group(function(){
        Route::get('/login', 'Auth\ManagerLoginController@showLoginForm')->name('manager.login');
        Route::post('/login', 'Auth\ManagerLoginController@login')->name('manager.login.submit');
        Route::get('/logout', 'Auth\ManagerLoginController@logout')->name('manager.logout');
        Route::get('/', 'ManagerController@index')->name('manager.dashboard');
    });

    //vendor routes
    Route::prefix('vendor')->group(function(){
        Route::get('/login', 'Auth\VendorLoginController@showLoginForm')->name('vendor.login');
        Route::post('/login', 'Auth\VendorLoginController@login')->name('vendor.login.submit');
        Route::get('/logout', 'Auth\VendorLoginController@logout')->name('vendor.logout');
        Route::get('/', 'VendorController@index')->name('vendor.dashboard');
    });

    //sales associate routes
    Route::prefix('sales-associate')->group(function(){
        Route::get('/login', 'Auth\SalesAssociateLoginController@showLoginForm')->name('sales-associate.login');
        Route::post('/login', 'Auth\SalesAssociateLoginController@login')->name('sales-associate.login.submit');
        Route::get('/logout', 'Auth\SalesAssociateLoginController@logout')->name('sales-associate.logout');
        Route::get('/', 'SalesAssociateController@index')->name('sales-associate.dashboard');
    });

    //delivery partner routes
    Route::prefix('delivery-partner')->group(function(){
        Route::get('/login', 'Auth\DeliveryPartnerLoginController@showLoginForm')->name('delivery-partner.login');
        Route::post('/login', 'Auth\DeliveryPartnerLoginController@login')->name('delivery-partner.login.submit');
        Route::get('/logout', 'Auth\DeliveryPartnerLoginController@logout')->name('delivery-partner.logout');
        Route::get('/', 'DeliveryPartnerController@index')->name('delivery-partner.dashboard');
    });
});

