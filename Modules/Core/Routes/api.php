<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/core', function (Request $request) {
    return $request->user();
});

Route::middleware([localization::class])->group(function(){
    Route::get('/ads', 'AdsController@index');
    Route::post('/add-ads', 'AdsController@storeApi');
});