<?php

use Illuminate\Http\Request;

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
    Route::group(['prefix' => 'admin/catalog', 'as' => 'admin.catalog.', 'middleware' => ['language']], function () {

        // Catalog Family Routes
        Route::get('/families', 'AttributeFamilyController@index')->name('families.index');
        Route::post('/families', 'AttributeFamilyController@store')->name('families.store');
        Route::get('/families/{family}', 'AttributeFamilyController@show')->name('families.show');
        Route::post('/families/{family}', 'AttributeFamilyController@update')->name('families.update');
        Route::delete('/families/{family}', 'AttributeFamilyController@destroy')->name('families.delete');


        // Catalog Attribute Group Routes
        Route::get('/attribute-group', 'AttributeGroupController@index')->name('attribute-groups.index');
        Route::post('/attribute-group', 'AttributeGroupController@store')->name('attribute-groups.store');
        Route::get('/attribute-group/{id}', 'AttributeGroupController@show')->name('attribute-groups.show');
        Route::post('/attribute-group/{id}', 'AttributeGroupController@update')->name('attribute-groups.update');
        Route::delete('/attribute-group/{id}', 'AttributeGroupController@destroy')->name('attribute-groups.delete');


        Route::get('/attributes', 'AttributeController@index')->name('attributes.index');
        Route::post('/attributes', 'AttributeController@store')->name('attributes.store');
        Route::get('/attributes/{id}', 'AttributeController@show')->name('attributes.show');
        Route::post('/attributes/{id}', 'AttributeController@update')->name('attributes.update');
        Route::delete('/attributes/{id}', 'AttributeController@destroy')->name('attributes.delete');
        Route::post('/attributes/mass-delete', 'AttributeController@massDestroy')->name('attributes.mass-delete');


    });
});
