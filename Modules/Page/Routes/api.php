<?php

use Modules\Page\Http\Controllers\PageAvailabilityController;

Route::group(['middleware' => ['api']], function () {
    //ADMIN PAGES ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {

        Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
            Route::put("/{page_id}/allow-page", [PageAvailabilityController::class, "allowPage"])->name("allow_page");
            Route::delete("/delete-allow-page", [PageAvailabilityController::class, "deleteAllowPage"])->name("delete_allow_page");
            Route::get("/model-list", [PageAvailabilityController::class, "modelList"])->name("model_list");
            Route::put("/{page_id}/status", [\Modules\Page\Http\Controllers\PageController::class, "updateStatus"])->name("status");

            Route::resource("configurations", PageConfigurationController::class)->only(["store", "show", "destroy"]);
            Route::resource('images', PageImageController::class)->only(['store', 'destroy']);
        });

        Route::resource('pages', PageController::class)->except(['create', 'edit','show']);
    });
});
