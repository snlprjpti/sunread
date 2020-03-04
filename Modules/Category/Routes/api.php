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

    //ADMIN USER ROUTES
    Route::group(['prefix'=>'admin/catalog','as' => 'admin.catalog.','middleware' => ['language']],function () {
        Route::resource('categories' ,'CategoryController');
    });
});