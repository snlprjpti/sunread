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
    Route::group(["prefix" => "admin/erp", "middleware" => ["admin", "language"], "as" => "admin.erp."], function () {        
        Route::resource("erp", ErpController::class)->except(["create","edit"]);
    });
    
});