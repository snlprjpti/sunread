<?php

use Modules\Customer\Http\Controllers\ResetPasswordController;
use Modules\Customer\Http\Controllers\VerificationController;

Route::get("/{channel_code}/{store_code}/verify-account/{token}", [VerificationController::class, "verifyAccount"])->name("account-verify");

Route::get("/{channel_code}/{store_code}/reset-password/{token}", [ResetPasswordController::class, "create"])->name("customers.reset-password.create");
