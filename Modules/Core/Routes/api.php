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


Route::group(['middleware' => ['api']], function () {

    //ADMIN ATTRIBUTE ROUTES
    Route::group(['prefix' => 'admin', 'middleware' => ['language']], function () {

        // Locale Routes
        Route::get('/locales', 'LocaleController@index')->name('admin.locales.index');
        Route::post('/locales', 'LocaleController@store')->name('admin.locales.store');
        Route::get('/locales/{locale}', 'LocaleController@show')->name('admin.locales.show');
        Route::put('/locales/{locale}', 'LocaleController@update')->name('admin.locales.update');
        Route::delete('/locales/{locale}', 'LocaleController@destroy')->name('admin.locales.delete');

    });
});
