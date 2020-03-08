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
        Route::get('/attribute-group', 'AttributeGroupController@index')->name('attribute-group.index');
        Route::post('/attribute-group', 'AttributeGroupController@store')->name('attribute-group.store');
        Route::get('/attribute-group/{attribute-group}', 'AttributeGroupController@show')->name('attribute-group.show');
        Route::post('/attribute-group/{attribute-group}', 'AttributeGroupController@update')->name('attribute-group.update');
        Route::delete('/attribute-group/{attribute-group}', 'AttributeGroupController@destroy')->name('attribute-group.delete');


        Route::get('/attributes', 'AttributeController@index')->name('attributes.index');
        Route::post('/attributes', 'AttributeController@store')->name('attributes.store');
        Route::get('/attributes/{id}', 'AttributeController@show')->name('attributes.show');
        Route::post('/attributes/{id}', 'AttributeController@update')->name('attributes.update');
        Route::delete('/attributes/{id}', 'AttributeController@destroy')->name('attributes.delete');
        Route::post('/attributes/mass-delete', 'AttributeController@massDestroy')->name('attributes.mass-delete');


    });
});
