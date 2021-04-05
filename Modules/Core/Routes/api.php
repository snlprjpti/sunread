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
    //ADMIN ATTRIBUTE ROUTES
    Route::group(['prefix' => 'admin', 'middleware' => ['admin', 'language'], 'as' => 'admin.'], function () {
        // Activities Routes
        Route::delete('activities/bulk', 'ActivityController@bulkDelete')->name('activities.bulk-delete');
        Route::resource('activities', 'ActivityController')->only(['index', 'show', 'destroy']);

        // Locale Routes
        Route::resource('locales', 'LocaleController')->except(['create', 'edit']);

        // Currency Routes
        Route::resource('currencies', 'CurrencyController')->except(['create', 'edit']);

        // Exchange Rates Routes
        Route::resource('exchange_rates', 'ExchangeRateController')->except(['create', 'edit']);

        // Channels Routes
        Route::resource('channels', 'ChannelController')->except(['create', 'edit']);
    });
});
