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
Route::group(['prefix' => LaravelLocalization::setLocale()], function(){
    Route::prefix('admin')->group(function () {
        
        Route::prefix('vendor_types')->as('vendor_types.')->group(function() {
            Route::get('/manage', 'TypesOfVendorsController@manage')->name('manage');
            Route::get('/vendors/{id}', 'TypesOfVendorsController@vendors')->name('vendors');
            Route::post('/image-add', 'TypesOfVendorsController@addImage')->name('image_add');
            Route::post('/image-remove/{id}', 'TypesOfVendorsController@removeImage')->name('image_remove');
            
        });
        Route::resource('vendor_types', TypesOfVendorsController::class);
    
        Route::prefix('vendors')->as('vendors.')->group(function() {
            Route::get('/manage', 'VendorsController@manage')->name('manage');
            Route::get('/user/{id}', 'VendorsController@UserVendor');
            Route::get('/products/{id}', 'VendorsController@vendorProducts');
            Route::post('/image-add', 'VendorsController@addImage')->name('image_add');
            Route::get('/images/{id}', 'VendorsController@Images');
        });
        Route::resource('vendors', VendorsController::class);

        Route::prefix('vendor_status')->as('vendor_status.')->group(function() {
            Route::get('/manage', 'VendorsStatusController@manage')->name('manage');
            Route::get('/user/{id}', 'VendorsStatusController@UserVendor');
        });
        Route::resource('vendor_status', VendorsStatusController::class);

        Route::prefix('coupons')->as('coupons.')->group(function() {
            Route::get('/manage', 'CouponsController@manage')->name('manage');
            Route::post('store-for-vendor', 'CouponsController@storeForVendor')->name('store-for-vendor');
            Route::post('update-for-vendor/{id}', 'CouponsController@updateForVendor')->name('update-for-vendor');
            Route::post('change-status/{id}', 'CouponsController@changeStatusForAdmin')->name('change-status-for-admin');
            
        });
        Route::resource('coupons', CouponsController::class);
        
        Route::prefix('offers')->as('offers.')->group(function() {
            Route::get('/manage', 'OffersController@manage')->name('manage');
            Route::post('/image-add', 'OffersController@addImage')->name('image_add');
            Route::post('/image-remove/{id}', 'OffersController@removeImage')->name('image_remove');
            Route::post('store-for-vendor', 'OffersController@storeForVendor')->name('store-for-vendor');
            Route::post('update-for-vendor/{id}', 'OffersController@updateForVendor')->name('update-for-vendor');
            Route::post('change-status/{id}', 'OffersController@changeStatusForAdmin')->name('change-status-for-admin');

        });
        Route::resource('offers', OffersController::class);
    });
});
