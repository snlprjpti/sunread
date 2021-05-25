<?php

use Illuminate\Http\Request;


Route::group(["middleware" => ["api"]], function() {
    // ADMIN COUPON ROUTES
    Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api","admin", "language"]], function() {
        Route::post("/coupons/{coupon_id}/allow-coupon", [\Modules\Coupon\Http\Controllers\AllowCouponController::class,"allowCoupon"])->name('coupons.allow_coupon');
        Route::delete("/coupons/delete-allow-coupon", [\Modules\Coupon\Http\Controllers\AllowCouponController::class,"deleteAllowCoupon"])->name('coupons.delete_allow_coupon');
        Route::get("/coupons/model-list", [\Modules\Coupon\Http\Controllers\CouponController::class,"modelList"])->name('coupons.model_list');
        Route::resource("/coupons", CouponController::class);
    });
});
