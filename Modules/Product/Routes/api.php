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

        Route::get('/products', 'ProductController@index')->name('admin.catalog.products.index');
        Route::post('/products', 'ProductController@store')->name('products.store');

        Route::get('/products/{id}', 'ProductController@edit')->name('products.edit');
        Route::put('/products/{id}', 'ProductController@update')->name('products.update');

        Route::post('/products/upload-file/{id}', 'ProductController@uploadLink')->name('products.upload_link');
        Route::post('/products/upload-sample/{id}', 'ProductController@uploadSample')->name('products.upload_sample');
        Route::post('/products/delete/{id}', 'ProductController@destroy')->name('products.delete');

    });
});
