<?php

use Illuminate\Http\Request;


Route::group(["middleware" => ["api"]], function() {
    // ADMIN COUPON ROUTES
    Route::group(["prefix" => "admin/coupon", "as" => "admin.catalog.", "middleware" => ["admin", "language"]], function() {
        Route::resource("/", CouponController::class);
    });
});
