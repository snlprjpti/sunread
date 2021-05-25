<?php

use Illuminate\Http\Request;


Route::group(["middleware" => ["api"]], function() {
    // ADMIN COUPON ROUTES
    Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api","admin", "language"]], function() {
        Route::post("/coupons/{coupon_id}/allow_coupon", [\Modules\Coupon\Http\Controllers\AllowCouponController::class,"allowCoupon"])->name('coupons.allow_coupon');
        Route::delete("/coupons/delete_allow_coupon", [\Modules\Coupon\Http\Controllers\AllowCouponController::class,"deleteAllowCoupon"])->name('coupons.delete_allow_coupon');
        Route::resource("/coupons", CouponController::class);
    });
});
