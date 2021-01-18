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


//USER MODULE ROUTES

Route::group(['middleware' => ['api']], function () {

    //ADMIN USER ROUTES
    Route::group(['prefix'=>'admin','as' => 'admin.'],function () {

        // Session Routes
        Route::post('/login', 'SessionController@login')->name('session.login');
        Route::get('/logout', 'SessionController@logout')->name('session.logout');
        Route::post('/forget-password', 'ForgotPasswordController@store')->name('forget-password.store');
        Route::post('/reset-password', 'ResetPasswordController@store')->name('reset-password.store');
        Route::get('/reset-password/{token}', 'ResetPasswordController@create')->name('reset-password.create');

        Route::group(['middleware' => 'jwt.verify'],function(){
            Route::resource('roles' ,'RoleController');
            Route::resource('users' ,'UserController');
            Route::get('/account', 'AccountController@edit')->name('account.edit');
            Route::put('/account', 'AccountController@update')->name('account.update');
        });
    });

});