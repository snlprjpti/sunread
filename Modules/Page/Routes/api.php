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
    //ADMIN PAGES ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {
        Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
            Route::post("/{page_id}/allow-page", [\Modules\Page\Http\Controllers\PageAvailabilityController::class, "allowPage"])->name('allow_page');
            Route::delete("/delete-allow-page", [\Modules\Page\Http\Controllers\PageAvailabilityController::class, "deleteAllowPage"])->name('delete_allow_page');
            Route::get("/model-list", [\Modules\Page\Http\Controllers\PageAvailabilityController::class, "modelList"])->name('model_list');
            Route::put("/pages/{page_id}/update-status", [\Modules\Page\Http\Controllers\PageController::class, "updateStatus"])->name("status");
        });
        Route::resource('pages', PageController::class)->except(['create', 'edit']);
    });
});
