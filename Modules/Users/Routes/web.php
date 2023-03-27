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
        
        Route::prefix('users')->as('users.')->group(function() {
            Route::post('/change-password', 'UsersController@ChangePassword')->name('changePassword');
            Route::post('/change-password-for-users/{id}', 'UsersController@changePasswordForUser')->name('changePasswordForUser');
            Route::get('/manage', 'UsersController@manage')->name('manage');
            Route::post('store-for-vendor', 'UsersController@storeForVendor')->name('store-for-vendor');
            Route::post('update-for-vendor/{id}', 'UsersController@updateForVendor')->name('update-for-vendor');
        });
        Route::resource('users', UsersController::class);
    
        Route::prefix('roles')->as('roles.')->group(function() {
            Route::get('/manage', 'RolesController@manage')->name('manage');
            Route::get('/sub-roles/{id}', 'RolesController@subRoles')->name('sub_role');
        });
        Route::resource('roles', RolesController::class);

        Route::prefix('user_status')->as('user_status.')->group(function() {
            Route::get('/manage', 'UsersStatusController@manage')->name('manage');
        });
        Route::resource('user_status', UsersStatusController::class);

        Route::prefix('otps')->as('otps.')->group(function() {
            Route::get('/manage', 'ArchiveOtpsController@manage')->name('manage');
        });
        Route::resource('otps', ArchiveOtpsController::class);
        
        Route::prefix('permissions')->group(function () {
            Route::get('manage', 'PermissionsController@manage');
        });
        Route::resource('permissions', PermissionsController::class);
    });
});

