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
    //ADMIN CATEGORY ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {
        Route::put("/pages/{page_id}/update-status", [\Modules\Page\Http\Controllers\PageController::class, "updateStatus"])->name("pages.status");
        Route::resource('pages', PageController::class)->except(['create', 'edit']);
    });
});
