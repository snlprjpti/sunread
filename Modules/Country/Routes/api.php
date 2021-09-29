<?php

Route::group(['middleware' => ['api']], function () {
    //ADMIN COUNTRY ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {

        Route::resource('country', CountryController::class)->only(['index', 'show']);
        Route::resource('regions', RegionController::class)->only(['index', 'show']);
        Route::resource('cities', CityController::class)->only(['index', 'show']);
        Route::get('/country/{country_id}/regions', [Modules\Country\Http\Controllers\RegionController::class, 'countryWiseRegion'])->name('country.regions.show');
    });

//    PUBLIC COUNTRY ROUTES
    Route::group(['prefix'=> 'public/countries', 'as' => 'public.'], function () {

        Route::get('/', [ \Modules\Country\Http\Controllers\StoreFront\CountryController::class, "index" ]);
        Route::get('/{country_id}/regions', [ \Modules\Country\Http\Controllers\StoreFront\RegionController::class, "index" ]);
        Route::get('/{country_id}/regions/{region_id}/cities', [ \Modules\Country\Http\Controllers\StoreFront\CityController::class, "index" ]);
    });
});
