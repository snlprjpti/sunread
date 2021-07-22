<?php

use Modules\Page\Http\Controllers\PageAvailabilityController;

Route::group(['middleware' => ['api']], function () {
    //ADMIN PAGES ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {
        Route::get('pages/components', [\Modules\Page\Http\Controllers\PageAttributeController::class, "index"])->name("components.index");
        Route::get('pages/components/{component_slug}', [\Modules\Page\Http\Controllers\PageAttributeController::class, "show"])->name("components.show");
        Route::resource('pages', PageController::class)->except(['create', 'edit']);
    });
});
