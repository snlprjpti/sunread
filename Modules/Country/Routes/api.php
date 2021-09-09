<?php

Route::group(['middleware' => ['api']], function () {
    //ADMIN COUNTRY ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {

        Route::resource('country', CountryController::class)->only(['index', 'show']);
        Route::resource('regions', RegionController::class)->only(['index', 'show']);
        Route::resource('cities', CityController::class)->only(['index', 'show']);
    });

//    PUBLIC COUNTRY ROUTES
    Route::group(['prefix'=> 'public', 'as' => 'public.'], function () {

        Route::get('country', [ \Modules\Country\Http\Controllers\StoreFront\CountryController::class, "index" ]);
        Route::get('regions/{country_id}', [ \Modules\Country\Http\Controllers\StoreFront\RegionController::class, "index" ]);
        Route::get('cities/{country_id}/{region_id}', [ \Modules\Country\Http\Controllers\StoreFront\CityController::class, "index" ]);
    });
});
