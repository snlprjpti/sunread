<?php

use Modules\User\Http\Controllers\AccountController;
use Modules\User\Http\Controllers\SessionController;
use Modules\User\Http\Controllers\ResetPasswordController;
use Modules\User\Http\Controllers\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//USER MODULE ROUTES
Route::group(["middleware" => ["api"], "prefix" => "admin", "as" => "admin."], function() {
    // Session Routes
    Route::post("/login", [SessionController::class, "login"])->name("session.login");
    Route::get("/logout", [SessionController::class, "logout"])->name("session.logout")->middleware("jwt.verify");
    Route::post("/forget-password", [ForgotPasswordController::class, "store"])->name("forget-password.store");
    Route::post("/reset-password", [ResetPasswordController::class, "store"])->name("reset-password.store");
    Route::get("/reset-password/{token}", [ResetPasswordController::class, "create"])->name("reset-password.create");

    Route::group(["middleware" => "jwt.verify"], function() {
        // Roles Routes
        Route::get("permissions", [\Modules\User\Http\Controllers\RoleController::class, "fetchPermission"]);
        Route::resource("roles", RoleController::class);

        // User Routes
        Route::resource("users", UserController::class);

        // Account Routes
        Route::get("/account", [AccountController::class, "show"])->name("account.show");
        Route::put("/account", [AccountController::class, "update"])->name("account.update");
        Route::post("/account/image", [AccountController::class, "uploadProfileImage"])->name("image.update");
        Route::delete("/account/image", [AccountController::class, "deleteProfileImage"])->name("image.delete");
    });
});
