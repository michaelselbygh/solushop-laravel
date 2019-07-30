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



//home and auth routes
Route::get('/', 'AppHomeController@showHome')->name('home');
Route::get('/logout', 'Auth\LoginController@logout')->name('customer.logout');
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::get('/register', 'Auth\LoginController@showRegisterForm')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('customer.login.submit');
Route::get('/reset-password', 'Auth\LoginController@showResetPasswordForm')->name('customer.reset.password');
Route::post('/reset-password', 'Auth\LoginController@resetPassword')->name('customer.reset.password.submit');

//shop, products and vendors routes
Route::get('/shop/category/{categorySlug}', 'AppShopController@showCategoryProducts')->name('show.shop.category');
Route::get('/shop/{vendorSlug}/{productSlug}', 'AppProductController@showProduct')->name('show.product');
Route::post('/shop/{vendorSlug}/{productSlug}', 'AppProductController@processProductAction')->name('process.product.action');
Route::get('/shop/{vendorSlug}', 'AppVendorController@showVendor')->name('show.vendor');
Route::get('/shops', 'AppVendorController@showVendors')->name('show.vendors');
Route::get('/shop', 'AppShopController@showProducts')->name('show.shop');
Route::post('/shop', 'AppShopController@showSearchProducts')->name('show.shop.search');

//my account routes
Route::get('/my-account', 'AppMyAccountPagesController@showProfile')->name('show.account');

//general pages 
Route::get('/wishlist', 'AppGeneralPagesController@showWishlist')->name('show.wishlist');
Route::post('/wishlist', 'AppGeneralPagesController@processWishlistAction')->name('process.wishlist.action');
Route::get('/cart', 'AppGeneralPagesController@showCart')->name('show.cart');
Route::post('/cart', 'AppGeneralPagesController@processCartAction')->name('process.cart.action');
Route::get('/checkout', 'AppGeneralPagesController@showCheckout')->name('show.checkout');
Route::post('/checkout', 'AppGeneralPagesController@processCheckout')->name('process.checkout');
Route::get('/about', 'AppGeneralPagesController@showAbout')->name('show.about');
Route::get('/contact', 'AppGeneralPagesController@showContact')->name('show.contact');
Route::get('/terms-and-conditions', 'AppGeneralPagesController@showTNC')->name('show.terms.and.conditions');
Route::get('/privacy-policy', 'AppGeneralPagesController@showPrivacyPolicy')->name('show.privacy.policy');
Route::get('/return-policy', 'AppGeneralPagesController@showReturnPolicy')->name('show.return.policy');
Route::get('/frequently-asked-questions', 'AppGeneralPagesController@showFAQ')->name('show.frequently.asked.questions');
Route::get('/page-not-found', 'AppGeneralPagesController@show404')->name('page.not.found');

//crons for testing purposes
Route::get('/cron/reports', 'CronsController@generateReports');
Route::get('/cron/process-sms-queue', 'CronsController@processSMSQueue');
Route::get('/cron/update-counts', 'CronsController@updateCounts');
Route::get('/cron/vendor-subscriptions-check', 'CronsController@checkVendorSubscriptions');


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

