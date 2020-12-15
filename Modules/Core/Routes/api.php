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

        // Currency Routes
        Route::get('/currencies', 'CurrencyController@index')->name('admin.currencies.index');
        Route::post('/currencies', 'CurrencyController@store')->name('admin.currencies.store');
        Route::get('/currencies/{currency}', 'CurrencyController@show')->name('admin.currencies.show');
        Route::put('/currencies/{currency}', 'CurrencyController@update')->name('admin.currencies.update');
        Route::delete('/currencies/{currency}', 'CurrencyController@destroy')->name('admin.currencies.delete');

        // Exchange Rates Routes
        Route::get('/exchange_rates', 'ExchangeRateController@index')->name('admin.exchange_rates.index');
        Route::get('/exchange_rates', 'ExchangeRateController@create')->name('admin.exchange_rates.create');
        Route::post('/exchange_rates', 'ExchangeRateController@store')->name('admin.exchange_rates.store');
        Route::get('/exchange_rates/{id}', 'ExchangeRateController@edit')->name('admin.exchange_rates.edit');
        Route::get('/exchange_rates/{id}', 'ExchangeRateController@updateRates')->name('admin.exchange_rates.update-rates');
        //Route::put('/exchange_rates/edit/{id}', 'ExchangeRateController@update')->name('admin.exchange_rates.update');
        Route::delete('/exchange_rate/{id}', 'ExchangeRateController@destroy')->name('admin.exchange_rates.delete');



    });
});
