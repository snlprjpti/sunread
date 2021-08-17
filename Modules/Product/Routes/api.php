<?php

Route::group(["middleware" => ["api"]], function() {
    // ADMIN PRODUCT ROUTES
    Route::group(["prefix" => "admin/catalog", "as" => "admin.catalog.", "middleware" => ["admin", "language"]], function() {

        //Product Attribute Routes 
        Route::post('products/attributes', [\Modules\Product\Http\Controllers\ProductAttributeController::class, "store"])->name("products.attributes.store");
        
        Route::get('products/search', [\Modules\Product\Http\Controllers\ProductSearchController::class, "index"]);

        Route::post('products/bulk-reindex', [\Modules\Product\Http\Controllers\ProductSearchController::class, "bulkReIndex"])->name('products.bulk-reindex');
        Route::get('products/{id}/reindex', [\Modules\Product\Http\Controllers\ProductSearchController::class, "reIndex"])->name('products.reindex');

        // Catalog Product Routes
        Route::put("/products/{product_id}/status", [\Modules\Product\Http\Controllers\ProductController::class, "updateStatus"])->name("products.status");
        Route::resource("products", ProductController::class)->except(["create", "edit"]);
        Route::get("product/attributes/{id}", [\Modules\Product\Http\Controllers\ProductController::class, "product_attributes"])->name("products.attributes.show");

        Route::resource("configurable-products", ProductConfigurableController::class)->except(["create", "edit", "index", "show"]);

        // Product Images Routes
        Route::group(['prefix' => 'product', 'as' => 'products.'], function() {
            Route::put('image/{id}/change-main-image', [\Modules\Product\Http\Controllers\ProductImageController::class,"changeMainImage"])->name("image.change_main_image");
            Route::resource('image', ProductImageController::class)->only(['store', 'destroy']);
            Route::put('images/update/{id}', [\Modules\Product\Http\Controllers\ProductImageController::class, "updateImage"])->name("images.update-image");
        });



        // Product Images Routes
        Route::get('products/{category_id}/category', [\Modules\Product\Http\Controllers\ProductController::class,"categoryWiseProducts"])->name("products.categoryWiseProducts");
    });
});

Route::group(['prefix'=>'public', 'as' => 'public.'], function () {
    Route::get('catalog/category/{category_id}/products', [\Modules\Product\Http\Controllers\StoreFront\ProductController::class, "index"])->name("products.index");
    Route::get('catalog/category/{category_id}/navigation/layered', [\Modules\Product\Http\Controllers\StoreFront\ProductController::class, "filter"])->name("products.filter");
});
