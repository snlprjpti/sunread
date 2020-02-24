<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['web']], function () {
    Route::prefix('admin')->group(function () {

        Route::get('/', 'Modules\Admin\Http\Controllers\Controller@redirectToLogin');

        // Login Routes
        Route::get('/login', 'Modules\User\Http\Controllers\SessionController@create')->defaults('_config', [
            'view' => 'admin::users.sessions.create'
        ])->name('admin.session.create');

        //login post route to admin auth controller
        Route::post('/login', 'Modules\User\Http\Controllers\SessionController@store')->defaults('_config', [
            'redirect' => 'admin.dashboard.index'
        ])->name('admin.session.store');

    });
});
