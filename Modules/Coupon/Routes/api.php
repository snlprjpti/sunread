<?php

use Illuminate\Http\Request;


Route::group(["middleware" => ["api"]], function() {
    // ADMIN COUPON ROUTES
    Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api","admin", "language"]], function() {
        Route::post("/coupons/{coupon_id}/allow_coupon", [\Modules\Coupon\Http\Controllers\AllowCouponController::class,"allowCoupon"])->name('coupons.allow_coupon');
        Route::post("/coupons/{coupon_id}/allow_multiple_coupon", [\Modules\Coupon\Http\Controllers\AllowCouponController::class,"allowMultipleCoupon"])->name('coupons.allow_multiple_coupon');
        Route::resource("/coupons", CouponController::class);
    });
});
