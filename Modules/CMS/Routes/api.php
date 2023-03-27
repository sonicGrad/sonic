<?php

use App\Http\Middleware\localization;
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

Route::middleware('auth:api')->get('/cms', function (Request $request) {
    return $request->user();
});
Route::middleware([localization::class])->group(function(){
    Route::post('/contact_us', 'ContactUsController@storeApi');
    Route::get('/about_us', 'TermsController@about_us');
    Route::get('/privacy_policy', 'TermsController@privacy_policy');
    Route::get('/terms', 'TermsController@terms');
    Route::get('/social_media_links', 'SocialMediaLinksController@index');
});