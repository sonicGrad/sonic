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

use Modules\Products\Entities\ProductVariation;

Route::group(['prefix' => LaravelLocalization::setLocale()], function(){
    Route::prefix('admin')->group(function () {

        Route::prefix('categories')->as('categories.')->group(function() {
            Route::get('/manage', 'CategoriesController@manage')->name('manage');
            Route::post('/image-add', 'CategoriesController@addImage')->name('image_add');
            Route::delete('/image-remove/{id}', 'CategoriesController@removeImage')->name('image_remove');
            Route::get('/vendor-categories/{id}', 'CategoriesController@vendorCategories')->name('vendor_categories');
        });
        Route::resource('categories', CategoriesController::class);

        Route::prefix('products')->as('products.')->group(function() {
            Route::get('/manage', 'ProductsController@manage')->name('manage');
            Route::get('/pending-products', 'ProductsController@pendingProducts')->name('pending_products');
            Route::get('/import-excel', 'ProductsController@createByExcel')->name('import_excel');
            Route::post('/import', 'ProductsController@import');
            Route::get('/export', 'ProductsController@export')->name('export');
            Route::get('/export-excel', 'ProductsController@exportExcel')->name('export_excel');
            Route::post('/image-add', 'ProductsController@addImage')->name('image_add');
            Route::post('/image-remove/{id}', 'ProductsController@removeImage')->name('image_remove');
            Route::post('store-for-vendor', 'ProductsController@storeForVendor')->name('store-for-vendor');
            Route::post('update-for-vendor/{id}', 'ProductsController@updateForVendor')->name('update-for-vendor');
            Route::post('change-status/{id}', 'ProductsController@changeStatusForAdmin')->name('change-status-for-admin');
            Route::post('attributes', 'ProductsController@addAttributesToProduct')->name('add-attributes-to-product');
            Route::post('product-variation/delete/{id}', 'ProductsController@productVariationDelete')->name('product-variation-delete');
        });
        Route::resource('products', ProductsController::class);

        Route::prefix('product_status')->as('product_status.')->group(function() {
            Route::get('/manage', 'ProductsStatusController@manage')->name('manage');
        });
        Route::resource('product_status', ProductsStatusController::class);

        Route::prefix('category_status')->as('category_status.')->group(function() {
            Route::get('/manage', 'CategoriesStatusController@manage')->name('manage');
        });
        Route::resource('category_status', CategoriesStatusController::class);

        Route::prefix('order_status')->as('order_status.')->group(function() {
            Route::get('/manage', 'OrdersStatusController@manage')->name('manage');
        });
        Route::resource('order_status', OrdersStatusController::class);
        
        Route::prefix('orders')->as('orders.')->group(function() {
            Route::get('/manage', 'OrdersController@manage')->name('manage');
            Route::post('change-status/{id}', 'OrdersController@changeStatusForAdmin')->name('change-status-for-admin');
            
        });

        Route::prefix('category_attribute_types')->as('category_attribute_types.')->group(function() {
            Route::get('/manage', 'CategoryAttributeTypesController@manage')->name('manage');
            Route::get('/category/{id}', 'CategoryAttributeTypesController@category')->name('category');
        });
        Route::resource('category_attribute_types', CategoryAttributeTypesController::class);

        
    });
});
