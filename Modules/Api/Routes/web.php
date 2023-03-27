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
Route::get('get-firebase-data', 'FirebaseController@index')->name('firebase.index');

Route::prefix('api')->group(function() {
    Route::get('/', 'ApiController@index');
});
