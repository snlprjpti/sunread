<?php

use Modules\Page\Http\Controllers\PageAvailabilityController;

Route::group(['middleware' => ['api']], function () {
    //ADMIN PAGES ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {

        Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
            Route::get('components', [\Modules\Page\Http\Controllers\PageAttributeController::class, "index"])->name("components.index");
            Route::get('components/{component_slug}', [\Modules\Page\Http\Controllers\PageAttributeController::class, "show"])->name("components.show");
            Route::resource('/', PageController::class)->except(['create', 'edit']);
        });
    });
});
