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
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => ['api']], function () {
    Route::prefix('admin')->group(function () {

        // Login Routes
        Route::post('/login', 'SessionController@login')->name('admin.session.login');

        //Roles Routes
        Route::get('/role', 'RoleController@index')->name('admin.role.index');
        Route::get('/role/show/{id}', 'RoleController@show')->name('admin.role.show');
        Route::get('/role/create', 'RoleController@create')->name('admin.role.create');
        Route::post('/role/create', 'RoleController@store')->name('admin.role.store');
        Route::get('/role/edit/{id}', 'RoleController@edit')->name('admin.role.edit');
        Route::put('/role/update/{id}', 'RoleController@update')->name('admin.role.update');
        Route::post('/role/delete/{id}', 'RoleController@destroy')->name('admin.role.delete');
    });
});