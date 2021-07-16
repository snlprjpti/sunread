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
    Route::group(['prefix'=>'admin/catalog', 'as' => 'admin.catalog.', 'middleware' => ['admin', 'language']], function () {
        Route::put("/categories/{category_id}/status", [\Modules\Category\Http\Controllers\CategoryController::class, "updateStatus"])->name("categories.status");
        Route::get('categories/attributes', [\Modules\Category\Http\Controllers\CategoryController::class, "attributes"])->name("categories.attributes");
        Route::put("categories/{category_id}/position", [\Modules\Category\Http\Controllers\CategoryController::class, "updatePosition"])->name("categories.position");
        Route::resource('categories', CategoryController::class)->except(['create', 'edit']);
    });
});
