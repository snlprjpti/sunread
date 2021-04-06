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
        Route::resource('families', 'AttributeFamilyController')->except(['create', 'edit']);

        // Catalog Attribute Group Routes
        Route::resource('attribute-groups', 'AttributeGroupController')->except(['create', 'edit']);

        // Attributes Routes
        Route::delete('attributes/bulk', 'AttributeController@bulkDelete')->name('attributes.bulk-delete');
        Route::resource('attributes', 'AttributeController')->except(['create', 'edit']);
    });
});
