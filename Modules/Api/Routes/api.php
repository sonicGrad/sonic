<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|

*/
Route::get('/vendorMenuCategory/{id}','ProductsController@vendorMenuCategory');
Route::group(['middleware' => 'api'], function($router) {
    Route::get('/registerPage', 'JWTController@registerPage');
    Route::post('/register','JWTController@register');
    Route::post('/login','JWTController@login');
    Route::post('/logout', 'JWTController@logout');
    Route::post('/refresh', 'JWTController@refresh');
    Route::post('/profile','JWTController@profile');
    Route::post('/send-code','JWTController@sendCode');
    Route::post('/check-code','JWTController@checkCode');
    Route::post('/change-password','JWTController@changePassword');
    Route::post('/change-password-when-login','JWTController@changePassWhenLogin');
    
    Route::post('/home-page','ProductsController@homePage');
    
    Route::post('add-user-image','UserController@addImage');
    Route::post('/update-profile','UserController@editProfile');
    Route::get('/coupons','UserController@coupons');
    Route::get('/setting-page','UserController@settingPage');
    Route::post('/favorites','ProductsController@favorites');
    Route::get('/cart-page','ProductsController@cartPage');
    Route::get('/filter-search','ProductsController@filterSearch');
    Route::post('/add-delete-form-cart','ProductsController@addAndDeleteFromCart');
    Route::get('/get-products-with-offer/{id}','ProductsController@getProductsUnderOffer');
    Route::post('/vendor/{id}','VendorController@vendorDetails');
    Route::post('/favorite','UserController@Favorite');
    Route::get('/address-book','UserController@addressBook');
    Route::post('/address-book','UserController@AddAddressBook');
    Route::post('/address-book/{id}','UserController@EditAddressBook');
    Route::delete('/remove-address-book/{id}','UserController@RemoveAddressBook');
    Route::post('/type-of-vendors/{id}','VendorController@viewAllTypesOfVendorsUnderCategoryOfVendors');
    Route::post('/type-fo-vendors-for-search/{id}','VendorController@viewAllTypesOfVendorsUnderCategoryOfVendorsForSearch');
    Route::get('/filleter-search-page','VendorController@filleterSearchPage');
    Route::post('/products/{id}','ProductsController@spesficProduct');
    Route::post('/make-order','ProductsController@makeOrder');
    Route::get('categories','ProductsController@categories');
    Route::post('apply-coupon','OrdersController@applyCoupon');
    Route::post('apply-offer','OrdersController@applyOffer');
    Route::post('checkout','OrdersController@checkout');
    Route::post('driver-accept-or-reject-order','OrdersController@DriverAcceptORRejectOrder');
    Route::post('order-states','OrdersController@OrderStates');
    Route::post('offers-for-user','OrdersController@offersForUser');
    Route::get('home-page-for-driver','DriverController@homePage');
    Route::get('order-list','DriverController@orderList');
    Route::post('order-details','DriverController@orderDetails');
    Route::get('list-of-status','DriverController@ListOFStatus');
    Route::post('add-new-state','DriverController@addNewState');
    Route::get('driver-profile','DriverController@driverProfile');
    Route::get('orders-confirm','DriverController@orderConfirm');
    Route::post('add-driver-image','DriverController@addImage');
    Route::post('update-driver-mobile-number','DriverController@updateMobileNumber');
    Route::post('update-driver-location','DriverController@updateLocation');
    Route::post('add-feedback-for-driver','DriverController@addFeedbackForDriver');
    Route::post('get-price','ProductsController@getPrice');
    Route::get('notification','UserController@notification');
    Route::get('check-order','OrdersController@checkOrder');
    Route::get('dispatchOrderToNewDriver','OrdersController@dispatchOrderToNewDriver');
    // Route::post('make-notification', function () {
    //     $user = auth()->guard('api')->user();
    //     // https://api.opencagedata.com/geocode/v1/json?q=31.54248%2C34.45228&key=6b85e2270825413a95e7ee5916383fb3&language=en&pretty=1
    //     $order =\Modules\Products\Entities\Orders::latest('id')->first();
    //     // return $order;
    //     // $location = json_decode($order->location);
    //     // $location2= $location->lat . '%2C' . $location->long;
    //     // $response = Http::get('https://api.opencagedata.com/geocode/v1/json', [
    //     //     'q' => $location2,
    //     //     'key' => '6b85e2270825413a95e7ee5916383fb3',
    //     //     'language' => 'ar',
    //     //     'pretty' => '1'
    //     // ]);
    //     // $result =  json_decode($response);
    //     // return $result->results[0]->formatted;
    //     $user->notify(new \Modules\Drivers\Notifications\NotifyDriverOfNewOrder($order));
        
    // });

    Route::get('type-of-vendors','VendorAppController@typeOfVendor');
    Route::post('add-logo-for-vendor','VendorAppController@addLogoForVendor');
    Route::get('list-of-orders','VendorAppController@listOfOrders');
    Route::get('order-details/{id}','VendorAppController@orderDetails');
    Route::get('products-for-vendor','VendorAppController@products');
    Route::post('store-product', 'VendorAppController@storeProduct');
    Route::post('attributes', 'VendorAppController@addAttributesToProduct');
    Route::post('/image-add-for-product', 'VendorAppController@addImageForProduct');
    Route::post('/image-add-for-offer', 'VendorAppController@addImageForOffer');
    Route::post('store-coupon', 'VendorAppController@couponStore');
    Route::post('store-offer', 'VendorAppController@offerStore');
    Route::get('rating-personage','VendorAppController@ratingPersonage');
    Route::get('rating-page','VendorAppController@ratingPage');
    Route::get('branches','VendorAppController@branches');
    Route::get('vendor-profile','VendorAppController@profile');
    Route::post('update-vendor-profile','VendorAppController@updateProfile');
    Route::get('home-page-for-vendor','VendorAppController@homePage');



    
});
