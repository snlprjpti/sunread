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
        Route::get('/roles', 'RoleController@index')->name('admin.roles.index');
        Route::post('/roles', 'RoleController@store')->name('admin.roles.store');
        Route::get('/roles/{id}', 'RoleController@show')->name('admin.roles.show');
        Route::put('/roles/{id}/update', 'RoleController@update')->name('admin.roles.update');
        Route::post('/roles/{id}/delete', 'RoleController@destroy')->name('admin.roles.delete');
    });
});