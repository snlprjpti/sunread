<?php

use Illuminate\Http\Request;
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

Route::group(['middleware' => ['api']], function () {

    //ADMIN USER ROUTES
    Route::group(['prefix'=>'admin','as' => 'admin.'],function () {

        // Session Routes
        Route::post('/login', [SessionController::class, 'login'])->name('session.login');
        Route::get('/logout', [SessionController::class, 'logout'])->name('session.logout')->middleware('jwt.verify');
        Route::post('/forget-password', [ForgotPasswordController::class, 'store'])->name('forget-password.store');
        Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('reset-password.store');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('reset-password.create');

        Route::group(['middleware' => 'jwt.verify'],function(){
            Route::resource('roles' ,'RoleController');
            Route::get('permissions' ,'RoleController@fetchPermission');
            Route::resource('users' ,'UserController');
            Route::post('/account/image', 'AccountController@uploadProfileImage')->name('image.update');
            Route::delete('/account/image', 'AccountController@deleteProfileImage')->name('image.delete');
            Route::get('/account', 'AccountController@edit')->name('account.edit');
            Route::put('/account', 'AccountController@update')->name('account.update');
        });
    });

});
