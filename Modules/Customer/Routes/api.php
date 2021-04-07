<?php
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
        Route::post('/register', 'RegistrationController@register')->name('register');
        // Session Routes
        Route::post('/login', 'SessionController@login')->name('session.login');
        Route::get('/logout', 'SessionController@logout')->name('session.logout');
        Route::post('/forget-password', 'ForgotPasswordController@store')->name('forget-password.store');
        Route::post('/reset-password', 'ResetPasswordController@store')->name('reset-password.store');
        Route::get('/reset-password/{token}', 'ResetPasswordController@create')->name('reset-password.create');
    });

    // ADMIN CUSTOMERS ROUTES
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['language', 'admin']],function () {
        // Customer Group Routes
        Route::resource('groups', 'CustomerGroupController')->except(['create', 'edit']);

        // Customer Routes
        Route::resource('customers', 'CustomerController')->except(['create', 'edit']);

        // Customer Address Routes
        Route::group(['prefix' => 'customers/{customers}', 'as' => 'customer.'], function() {
            Route::resource('addresses', 'AddressController')->except(['create', 'edit']);
        });
    });
});
