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

        Route::group(["prefix" => "navigation-menu/{navigation_menu_id}/", "as" => "navigation-menu."], function () {
            Route::get('items/attributes', [\Modules\NavigationMenu\Http\Controllers\NavigationMenuItemController::class, "attributes"])->name("items.attributes");
            Route::put('items/{navigation_menu_item_id}/status', [\Modules\NavigationMenu\Http\Controllers\NavigationMenuItemController::class, 'updateStatus'])->name('items.status');
            Route::resource('items', NavigationMenuItemController::class)->except(["create","edit"]);
        });
    });
});

Route::group(['prefix'=>'public', 'as' => 'public.'], function () {
    Route::get('/navigation-menus', [\Modules\NavigationMenu\Http\Controllers\StoreFront\NavigationMenuController::class, "index"])->name("navigation.menus.index");
});

