<?php

use Modules\Customer\Http\Controllers\CustomerAccountController;
use Modules\Customer\Http\Controllers\CustomerAddressAccountController;
use Modules\Customer\Http\Controllers\SessionController;
use Modules\Customer\Http\Controllers\RegistrationController;
use Modules\Customer\Http\Controllers\ResetPasswordController;
use Modules\Customer\Http\Controllers\ForgotPasswordController;
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

Route::group(['middleware' => ['api']], function () {
    // CUSTOMER ROUTES
    Route::group(['prefix' => 'customer', 'as' => 'customer.'],function () {
        Route::post('/register', [RegistrationController::class, 'register'])->name('register');
        // Session Routes
        Route::post('/login', [SessionController::class, 'login'])->name('session.login');
        Route::get('/logout', [SessionController::class, 'logout'])->middleware("jwt.verify")->name('session.logout');
        Route::post('/forget-password', [ForgotPasswordController::class, 'store'])->name('forget-password.store');
        Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('reset-password.store');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('reset-password.create');
    });

    Route::group(["middleware" => ["customer"], "prefix" => "customer", "as" => "customer."], function () {
        // CUSTOMER PROFILE
        Route::group(["prefix" => "accounts", "as" => "account."], function () {
            Route::get("/", [CustomerAccountController::class, "show"])->name("show");
            Route::put("/", [CustomerAccountController::class, "update"])->name("update");
            Route::post("image", [CustomerAccountController::class, "uploadProfileImage"])->name("image.update");
            Route::delete("image", [CustomerAccountController::class, "deleteProfileImage"])->name("image.delete");    
        });
        
        // CUSTOMER ADDRESS
        Route::group(["prefix" => "addresses", "as" => "address."], function () {
            Route::get("/", [CustomerAddressAccountController::class, "show"])->name("show");
            Route::post("/", [CustomerAddressAccountController::class, "create"])->name("create");
            Route::put("/{id}", [CustomerAddressAccountController::class, "update"])->name("update");
            Route::delete("/{id}", [CustomerAddressAccountController::class, "delete"])->name("delete");
        });
    });
    

    // ADMIN CUSTOMERS ROUTES
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['language', 'admin']],function () {
        // Customer Group Routes
        Route::resource('groups', CustomerGroupController::class)->except(['create', 'edit']);

        // Customer Routes
        Route::resource('customers', CustomerController::class)->except(['create', 'edit']);

        // Customer Address Routes
        Route::group(['prefix' => 'customers/{customers}', 'as' => 'customer.'], function() {
            Route::resource('addresses', AddressController::class)->except(['create', 'edit']);
        });
    });
});
