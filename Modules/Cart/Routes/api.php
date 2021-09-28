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

Route::group(['as' => 'cart.'], function () {
    
    // CART ROUTES
    Route::post("/public/checkout/cart", [\Modules\Cart\Http\Controllers\CartController::class, "addOrUpdateCart"])->name('add.update');
    Route::delete("/public/checkout/cart", [\Modules\Cart\Http\Controllers\CartController::class, "deleteProductFromCart"])->name('delete.product.from.cart');
    Route::get("/public/checkout/cart", [\Modules\Cart\Http\Controllers\CartController::class, "getAllProductFromCart"])->name('products.from.cart');
    Route::post("/public/checkout/cart/merge", [\Modules\Cart\Http\Controllers\CartController::class, "mergeCart"])->name('merge.cart')->middleware('customer');

});