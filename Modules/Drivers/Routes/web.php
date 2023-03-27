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
        
        Route::prefix('driver_types')->as('driver_types.')->group(function() {
            Route::get('/manage', 'DriversTypesController@manage')->name('manage');
            Route::get('/drivers/{id}', 'DriversTypesController@drivers')->name('drivers');
        });
        Route::resource('driver_types', DriversTypesController::class);
    
        Route::prefix('drivers')->as('drivers.')->group(function() {
            Route::get('/manage', 'DriversController@manage')->name('manage');
            Route::get('/driver-info/{id}', 'DriversController@driverInfo')->name('driverInfo');
            Route::get('/user/{id}', 'DriversController@UserVendor');
            Route::post('/license-image-add/{id}', 'DriversController@addLicenseImage')->name('image_remove');
            Route::post('/license-image-remove/{id}', 'DriversController@removeLicenseImage')->name('image_remove');
        });
        Route::resource('drivers', DriversController::class);

        Route::prefix('driver_status')->as('driver_status.')->group(function() {
            Route::get('/manage', 'DriversStatusController@manage')->name('manage');
        });
        Route::resource('driver_status', DriversStatusController::class);
    });
});
