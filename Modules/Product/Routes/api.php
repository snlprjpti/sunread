<?php


Route::group(["middleware" => ["api"]], function() {
    // ADMIN PRODUCT ROUTES
    Route::group(["prefix" => "admin/catalog", "as" => "admin.catalog.", "middleware" => ["admin", "language"]], function() {

        Route::get('products/search', [\Modules\Product\Http\Controllers\ProductSearchController::class,"search"]);
        Route::get('allproducts', [\Modules\Product\Http\Controllers\ProductSearchController::class,"productWithFilters"]);
        // Catalog Product Routes
        Route::resource("products", ProductController::class)->except(["create", "edit"]);
        // Product Images Routes
        Route::group(['prefix' => 'product', 'as' => 'products.'], function() {
            Route::put('image/{id}/change-main-image', [\Modules\Product\Http\Controllers\ProductImageController::class,"changeMainImage"])->name("image.change_main_image");
            Route::resource('image', ProductImageController::class)->only(['store', 'destroy']);
        });
    });
});
