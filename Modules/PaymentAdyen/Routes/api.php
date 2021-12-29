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
Route::group(['middleware' => ['api','proxies']], function () {
Route::post("checkout/payment/adyen/order/status", [\Modules\PaymentAdyen\Http\Controllers\PaymentAdyenController::class, "updateOrderStatus"])->name("adyen.update.order.status");
});