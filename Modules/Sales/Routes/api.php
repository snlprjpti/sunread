<?php

use Modules\Sales\Http\Controllers\OrderController;

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
        Route::post('orders/status/{order_id}', [OrderController::class, 'orderStatus'])->name('order.status');
        Route::resource("orders", OrderController::class)->only(["index", "show"]);
    });

    Route::group(['as' => 'sales.', 'prefix' => 'public'], function () {
        Route::resource("orders", \StoreFront\OrderController::class)->only(["store"]);
    });

});
