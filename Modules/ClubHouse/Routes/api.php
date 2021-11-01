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
    // ADMIN CLUBHOUSE ROUTES
    Route::group(["prefix" => "admin", "middleware" => ["admin", "language"], "as" => "admin."], function () {
        Route::get('clubhouses/attributes', [\Modules\ClubHouse\Http\Controllers\ClubHouseController::class, "attributes"])->name("clubhouse.attributes");
        Route::put('clubhouses/{club_house_id}/status', [\Modules\ClubHouse\Http\Controllers\ClubHouseController::class, 'updateStatus'])->name('clubhouse.status');
        Route::resource('clubhouses', ClubHouseController::class)->except(["create","edit"]);
    });
});
