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
    // ADMIN STOREFRONT ROUTES
    Route::group(["prefix" => "admin", "middleware" => ["admin", "language"], "as" => "admin."], function () {
        Route::resource('navigation-menus', NavigationMenuController::class)->except(["create","edit"]);

        Route::get('navigation-menu-items/attributes', [\Modules\NavigationMenu\Http\Controllers\NavigationMenuItemController::class, "attributes"])->name("navigation.menus.attributes");
        Route::put('navigation-menu-items/{navigation_menu_id}/status', [\Modules\NavigationMenu\Http\Controllers\NavigationMenuItemController::class, 'updateStatus'])->name('navigation.menus.status');
        Route::resource('navigation-menu-items', NavigationMenuItemController::class)->except(["create","edit"]);
    });
});

Route::group(['prefix'=>'public', 'as' => 'public.'], function () {
    Route::get('/navigation-menus', [\Modules\NavigationMenu\Http\Controllers\StoreFront\NavigationMenuController::class, "index"])->name("navigation.menus.index");
});

