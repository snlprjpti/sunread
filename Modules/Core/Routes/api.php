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
        Route::delete('activities/bulk', [\Modules\Core\Http\Controllers\ActivityController::class, 'bulkDelete'])->name('activities.bulk-delete');
        Route::resource('activities', ActivityController::class)->only(['index', 'show', 'destroy']);

        // Locale Routes
        Route::resource('locales', LocaleController::class)->except(['create', 'edit']);

        // Store Routes
        Route::resource('stores', StoreController::class)->except(['create','edit']);

        // Currency Routes
        Route::resource('currencies', CurrencyController::class)->except(['create', 'edit']);

        // Exchange Rates Routes
        Route::resource('exchange_rates', ExchangeRateController::class)->except(['create', 'edit']);

        // Channels Routes
        Route::resource('channels', ChannelController::class)->except(['create', 'edit']);
       
        // Websites Routes
        Route::resource('websites', WebsiteController::class)->except(['create', 'edit']);

        // Configurations Routes
        Route::resource('configurations', ConfigurationController::class)->except(['create', 'edit']);
    });
});
