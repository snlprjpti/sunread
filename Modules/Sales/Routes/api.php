<?php

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

Route::group(['middleware' => ['api','proxies']], function () {
    
    Route::group(['prefix' => 'admin/sales', 'as' => 'admin.sales.', 'middleware' => ['admin', 'language']], function () {
        Route::resource("orders", OrderController::class)->only(["index", "show"]);
    });

    Route::group(['as' => 'sales.', 'prefix' => 'public/orders'], function () {
        Route::resource("orders", \StoreFront\OrderController::class)->only(["store"]);
    });

});
