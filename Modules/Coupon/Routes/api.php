<?php

use Illuminate\Http\Request;


Route::group(["middleware" => ["api"]], function() {
    // ADMIN COUPON ROUTES
    Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api","admin", "language"]], function() {
        Route::post("/coupon/allow_coupon/{coupon_id}", [\Modules\Coupon\Http\Controllers\AllowCouponController::class,"allowCoupon"])->name('coupon.allow_coupon');
        Route::resource("/coupon", CouponController::class);
    });
});
