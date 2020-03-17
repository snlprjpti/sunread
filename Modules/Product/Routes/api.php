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


Route::group(['middleware' => ['api']], function () {

    //ADMIN CATEGORY ROUTES
    Route::group(['prefix'=>'admin/catalog','as' => 'admin.catalog.','middleware' => ['language']],function () {

        Route::get('/products', 'ProductController@index')->name('products.index');
        Route::post('/products', 'ProductController@store')->name('products.store');

        Route::get('/products/{product}', 'ProductController@show')->name('products.show');
        Route::post('/products/{product}', 'ProductController@update')->name('products.update');
        Route::delete('/products/{product}', 'ProductController@destroy')->name('products.delete');
        Route::get('/products/{product}/edit', 'ProductController@edit')->name('products.edit');

        //deletes the images table
        Route::post('/products/upload-file', 'ProductImageController@uploadFile')->name('products.upload-image');

        Route::post('/products/remove-file/{productImageId}', 'ProductImageController@removeFile')->name('products.remove-image');

    });
});
