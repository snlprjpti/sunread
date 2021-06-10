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
    Route::group(['prefix' => 'admin/catalog', 'as' => 'admin.catalog.', 'middleware' => ['admin', 'language']], function () {
        // Catalog Family Routes
        Route::put("/families/{families_id}/status", [\Modules\Attribute\Http\Controllers\AttributeFamilyController::class, "updateStatus"])->name('families.status');
        Route::resource('families', AttributeFamilyController::class)->except(['create', 'edit']);

        // Catalog Attribute Group Routes
        Route::resource('attribute-groups', AttributeGroupController::class)->except(['create', 'edit']);

        // Attributes Routes
        Route::delete('attributes/bulk', [\Modules\Attribute\Http\Controllers\AttributeController::class, 'bulkDelete'])->name('attributes.bulk-delete');
        Route::resource('attributes', AttributeController::class)->except(['create', 'edit']);
    });
});
