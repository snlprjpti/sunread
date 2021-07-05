<?php

Route::group(['middleware' => ['api']], function () {
    //ADMIN COUNTRY ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {

        Route::resource('country', CountryController::class)->only(['index', 'show']);
        Route::resource('regions', RegionController::class)->only(['index', 'show']);
        Route::resource('cities', CityController::class)->only(['index', 'show']);
    });
});
