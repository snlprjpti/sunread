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

Route::group(["middleware" => ["api"]], function() {
    // ADMIN PRODUCT ROUTES
    Route::group(["prefix" => "admin/catalog", "as" => "admin.catalog.", "middleware" => ["admin", "language"]], function() {
        // Catalog Product Routes
        Route::resource("products", ProductController::class)->except(["create", "edit"]);
        // Product Images Routes
        Route::group(['prefix' => 'product', 'as' => 'products.'], function() {
            Route::resource('image', ProductImageController::class)->only(['store', 'destroy']);
        });
    });
});
