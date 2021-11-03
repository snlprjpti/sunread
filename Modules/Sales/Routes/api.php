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

Route::group(['as' => 'order.', 'prefix' => 'public/checkout/order', 'middleware' => ['api','proxies']], function () {
    
    // CART ROUTES
    Route::post("/", [\Modules\Cart\Http\Controllers\OrderController::class, "sendOrder"])->name('send');

});
