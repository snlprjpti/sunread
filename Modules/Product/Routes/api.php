<?php


Route::group(["middleware" => ["api"]], function() {
    // ADMIN PRODUCT ROUTES
    Route::group(["prefix" => "admin/catalog", "as" => "admin.catalog.", "middleware" => ["admin", "language"]], function() {
        // Catalog Product Routes
        Route::resource("products", ProductController::class)->except(["create", "edit"]);
        // Product Images Routes
        Route::group(['prefix' => 'product', 'as' => 'products.'], function() {
            Route::resource('image', ProductImageController::class)->only(['store', 'destroy']);
            Route::get('image/set-main-image/{id}', [\Modules\Product\Http\Controllers\ProductImageController::class,"setMainImage"])->name("image.setMainImage");
        });
    });
});
