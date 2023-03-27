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
        
        Route::prefix('social_media_links')->as('social_media_links.')->group(function() {
            Route::get('/manage', 'SocialMediaLinksController@manage')->name('manage');
        });
        Route::resource('social_media_links', SocialMediaLinksController::class);

        Route::prefix('terms')->as('terms.')->group(function() {
            Route::get('/manage', 'TermsController@manage')->name('manage');
        });
        Route::resource('terms', TermsController::class);

        Route::prefix('contact_us')->as('contact_us.')->group(function() {
            Route::get('/manage', 'ContactUsController@manage')->name('manage');
            Route::get('/reply/{id}', 'ContactUsController@reply')->name('reply');
        });
        Route::resource('contact_us', ContactUsController::class);
    });
});
