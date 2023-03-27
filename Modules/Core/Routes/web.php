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
        
        Route::get('/dashboard', 'DashboardController@manage')->name('dashboard');
        Route::post('/features', 'FeaturesController@store')->name('features.store');
        Route::get('/features/{id}', 'FeaturesController@show')->name('features.show');
        Route::get('/features', 'FeaturesController@index')->name('features.index');
     
        Route::prefix('ads')->as('ads.')->group(function() {
            Route::get('/manage', 'AdsController@manage')->name('manage');
            Route::post('/image-add', 'AdsController@addImage')->name('image_add');
            Route::post('/image-remove/{id}', 'AdsController@removeImage')->name('image_remove');
            Route::post('store-for-vendor', 'AdsController@storeForVendor')->name('store-for-vendor');
            Route::post('update-for-vendor/{id}', 'AdsController@updateForVendor')->name('update-for-vendor');
            Route::post('change-status/{id}', 'AdsController@changeStatusForAdmin')->name('change-status-for-admin');

        });
        Route::resource('ads', AdsController::class);

        Route::prefix('county_province')->as('county_province.')->group(function() {
            Route::get('/manage', 'CountriesProvincesController@manage')->name('manage');
        });
        Route::resource('county_province', CountriesProvincesController::class);
    });
});