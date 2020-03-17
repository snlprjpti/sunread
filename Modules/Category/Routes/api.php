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

//USER MODULE ROUTES

Route::group(['middleware' => ['api']], function () {

    //ADMIN CATEGORY ROUTES
    Route::group(['prefix'=>'admin/catalog','as' => 'admin.catalog.','middleware' => ['language']],function () {
        Route::get('/categories' ,'CategoryController@index')->name('categories.index');
        Route::post('/categories' ,'CategoryController@store')->name('categories.store');
        Route::get('/categories/{category}' ,'CategoryController@show')->name('categories.show');
        Route::post('/categories/{category}' ,'CategoryController@update')->name('categories.update');
        Route::delete('/categories/{category}' ,'CategoryController@destroy')->name('categories.delete' );
    });
});