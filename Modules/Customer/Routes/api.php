<?php

use Illuminate\Http\Request;

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

//CUSTOMER MODULE ROUTES

Route::group(['middleware' => ['api']], function () {

    Route::group(['prefix'=>'customer','as' => 'customer.'],function () {

        Route::post('/register', 'RegistrationController@register')->name('register');

        // Session Routes
        Route::post('/login', 'SessionController@login')->name('session.login');
        Route::get('/logout', 'SessionController@logout')->name('session.logout');
        Route::post('/forget-password', 'ForgotPasswordController@store')->name('forget-password.store');
        Route::post('/reset-password', 'ResetPasswordController@store')->name('reset-password.store');
        Route::get('/reset-password/{token}', 'ResetPasswordController@create')->name('reset-password.create');

    });

    //ADMIN CATEGORY ROUTES
    Route::group(['prefix'=>'admin','as' => 'admin.catalog.','middleware' => ['language']],function () {
        Route::get('/customers' ,'CustomerController@index')->name('customers.index');
        Route::post('/customers' ,'CustomerController@store')->name('customers.store');
        Route::get('/customers/{customer}' ,'CustomerController@show')->name('customers.show');
        Route::put('/customers/{customer}' ,'CustomerController@update')->name('customers.update');
        Route::delete('/customers/{customer}' ,'CustomerController@destroy')->name('customers.delete' );
    });


    //Customer's addresses routes
    Route::get('customers/{customer_id}/addresses', 'AddressController@index')->name('admin.customer.addresses.index');
    Route::post('customers/{customer_id}/addresses', 'AddressController@store')->name('admin.customer.addresses.store');
    Route::get('customers/{customer_id}/addresses/{address_id}', 'AddressController@show')->name('admin.customer.addresses.show');
    Route::post('customers/{customer_id}/addresses/{address_id}', 'AddressController@update')->name('admin.customer.addresses.update');
    Route::delete('customers/{customer_id}/addresses/{address_id}', 'AddressController@destroy')->name('admin.customer.addresses.delete');
    Route::post('customers/{id}/addresses', 'AddressController@massDestroy')->name('admin.customer.addresses.massdelete');

});
