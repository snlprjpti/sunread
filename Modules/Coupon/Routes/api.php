<?php

use Illuminate\Http\Request;


Route::group(["middleware" => ["api"]], function() {
    // ADMIN COUPON ROUTES
    Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api","admin", "language"]], function() {
        Route::resource("/coupon", CouponController::class);
    });
});
