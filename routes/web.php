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
Route::get('/my-account', 'AppMyAccountController@showDashboard')->name('show.account.dashboard');
Route::get('/my-account/messages', 'AppMyAccountController@showMessages')->name('show.account.messages');
Route::get('/my-account/messages/{vendorSlug}/{productSlug?}', 'AppMyAccountController@showConversation')->name('show.account.conversation');
Route::post('/my-account/messages/{vendorSlug}/{productSlug?}', 'AppMyAccountController@processConversation')->name('process.account.conversation');
Route::get('/my-account/personal-details', 'AppMyAccountController@showPersonalDetails')->name('show.account.personal.details');
Route::post('/my-account/personal-details', 'AppMyAccountController@processPersonalDetails')->name('process.account.personal.details');
Route::get('/my-account/orders', 'AppMyAccountController@showOrders')->name('show.account.orders');
Route::post('/my-account/orders', 'AppMyAccountController@processOrders')->name('process.account.orders');
Route::get('/my-account/login-and-security', 'AppMyAccountController@showLoginAndSecurity')->name('show.account.login.and.security');
Route::post('/my-account/login-and-security', 'AppMyAccountController@processLoginAndSecurity')->name('process.account.login.and.security');
Route::get('/my-account/addresses', 'AppMyAccountController@showAddresses')->name('show.account.addresses');
Route::get('/my-account/s-wallet', 'AppMyAccountController@showWallet')->name('show.account.wallet');
Route::get('/my-account/addresses/add', 'AppMyAccountController@showAddAddress')->name('show.account.add.address');
Route::post('/my-account/addresses/add', 'AppMyAccountController@processAddAddress')->name('process.account.add.address');
Route::post('/my-account/addresses', 'AppMyAccountController@processEditAddress')->name('process.account.edit.address');
Route::post('/my-account/s-wallet', 'AppMyAccountController@processWallet')->name('process.account.wallet');

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
Route::get('/cron/delete-empty-conversations', 'CronsController@deleteEmptyConversations');
Route::get('/cron/delete-unpaid-orders', 'CronsController@deleteUnpaidOrders');
Route::get('/cron/delete-unpaid-wtu-payments', 'CronsController@deleteUnpaidWTUPayments');
Route::get('/cron/update-expired-coupons', 'CronsController@updateExpiredCoupons');

//portal routes
Route::prefix('portal')->group(function(){
    //manager routes
    Route::prefix('manager')->group(function(){
        //login routes
        Route::get('/login', 'Auth\ManagerLoginController@showLoginForm')->name('manager.login');
        Route::post('/login', 'Auth\ManagerLoginController@login')->name('manager.login.submit');
        Route::get('/logout', 'Auth\ManagerLoginController@logout')->name('manager.logout');

        //guides
        Route::get('/guide/delivery', 'ManagerController@showDeliveryGuide')->name('manager.show.delivery.guide');
        Route::get('/guide/pick-up', 'ManagerController@showPickUpGuide')->name('manager.show.pick.up.guide');

        //general pages
        
        Route::get('/customer/{customerID}', 'ManagerController@showCustomer')->name('manager.show.customer');
        Route::post('/customer/{customerID}', 'ManagerController@processCustomer')->name('manager.process.customer');
        Route::get('/customers', 'ManagerController@showCustomers')->name('manager.show.customers');
        Route::get('/order/{orderID}', 'ManagerController@showOrder')->name('manager.show.order');
        Route::post('/order/{orderID}', 'ManagerController@processOrder')->name('manager.process.order');
        Route::get('/orders', 'ManagerController@showOrders')->name('manager.show.orders');
        Route::post('/orders', 'ManagerController@processOrders')->name('manager.process.orders');
        Route::get('/messages/flagged', 'ManagerController@showFlaggedMessages')->name('manager.show.flagged.messages');
        Route::post('/messages/flagged', 'ManagerController@processFlaggedMessages')->name('manager.process.flagged.messages');
        Route::get('/messages/deleted', 'ManagerController@showDeletedMessages')->name('manager.show.deleted.messages');
        Route::get('/conversation/{conversationID}', 'ManagerController@showConversation')->name('manager.show.conversation');
        Route::post('/conversation/{conversationID}', 'ManagerController@processConversation')->name('manager.process.conversation');
        Route::get('/conversations', 'ManagerController@showMessages')->name('manager.show.messages');
        Route::get('/products', 'ManagerController@showProducts')->name('manager.show.products');
        Route::post('/products', 'ManagerController@processProducts')->name('manager.process.products');
        Route::get('/products/deleted', 'ManagerController@showDeletedProducts')->name('manager.show.deleted.products');
        Route::post('/products/deleted', 'ManagerController@processDeletedProducts')->name('manager.process.deleted.products');
        Route::get('/products/add', 'ManagerController@showAddProduct')->name('manager.show.add.product');
        Route::post('/products/add', 'ManagerController@processAddProduct')->name('manager.process.add.product');
        Route::get('/product/{productID}', 'ManagerController@showProduct')->name('manager.show.product');
        Route::post('/product/{productID}', 'ManagerController@processProduct')->name('manager.process.product');
        Route::get('/vendors', 'ManagerController@showVendors')->name('manager.show.vendors');
        Route::get('/vendors/add', 'ManagerController@showAddVendor')->name('manager.show.add.vendor');
        Route::post('/vendors/add', 'ManagerController@processAddVendor')->name('manager.process.add.vendor');
        Route::get('/vendor/{vendorSlug}', 'ManagerController@showVendor')->name('manager.show.vendor');
        Route::post('/vendor/{vendorSlug}', 'ManagerController@processVendor')->name('manager.process.vendor');
        Route::get('pick-ups/history', 'ManagerController@showPickupHistory')->name('manager.show.pick.ups.history');
        Route::get('pick-ups/active', 'ManagerController@showActivePickups')->name('manager.show.active.pick.ups');
        Route::post('pick-ups/active', 'ManagerController@processActivePickups')->name('manager.process.active.pick.ups');
        Route::get('deliveries/history', 'ManagerController@showDeliveryHistory')->name('manager.show.deliveries.history');
        Route::get('deliveries/active', 'ManagerController@showActiveDeliveries')->name('manager.show.active.deliveries');
        Route::post('deliveries/active', 'ManagerController@processActiveDeliveries')->name('manager.process.active.deliveries');
        Route::get('coupons', 'ManagerController@showCoupons')->name('manager.show.coupons');
        Route::get('coupons/generate', 'ManagerController@showGenerateCoupon')->name('manager.show.generate.coupon');
        Route::post('coupons/generate', 'ManagerController@processGenerateCoupon')->name('manager.process.generate.coupon');
        Route::get('/delivery-partners', 'ManagerController@showDeliveryPartners')->name('manager.show.delivery.partners');
        Route::get('/delivery-partners/add', 'ManagerController@showAddDeliveryPartner')->name('manager.show.add.delivery.partner');
        Route::post('/delivery-partners/add', 'ManagerController@processAddDeliveryPartner')->name('manager.process.add.delivery.partner');
        Route::get('/delivery-partner/{partnerID}', 'ManagerController@showDeliveryPartner')->name('manager.show.delivery.partner');
        Route::post('/delivery-partner/{partnerID}', 'ManagerController@processDeliveryPartner')->name('manager.process.delivery.partner');
        Route::get('/sales-associates', 'ManagerController@showSalesAssociates')->name('manager.show.sales.associates');
        Route::get('/sales-associates/add', 'ManagerController@showAddSalesAssociate')->name('manager.show.add.sales.associate');
        Route::post('/sales-associates/add', 'ManagerController@processAddSalesAssociate')->name('manager.process.add.sales.associate');
        Route::get('/sales-associate/{memberID}', 'ManagerController@showSalesAssociate')->name('manager.show.sales.associate');
        Route::post('/sales-associate/{memberID}', 'ManagerController@processSalesAssociate')->name('manager.process.sales.associate');
        Route::get('/accounts', 'ManagerController@showAccounts')->name('manager.show.accounts');
        Route::post('/accounts', 'ManagerController@processAccounts')->name('manager.process.accounts');
        Route::get('/subscriptions', 'ManagerController@showSubscriptions')->name('manager.subscriptions');
        Route::post('/subscriptions', 'ManagerController@processSubscriptions')->name('manager.process.subscriptions');
        Route::get('/activity-log', 'ManagerController@showActivityLog')->name('manager.activity.log');
        Route::get('/sms-report', 'ManagerController@showSMSReport')->name('manager.sms.report');
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

