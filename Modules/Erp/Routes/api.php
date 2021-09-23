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

use Modules\Erp\Jobs\Mapper\ErpMigrateProductJob;
use Modules\Erp\Jobs\Mapper\ErpMigrateVariantJob;

Route::get("admin/erp", function () {
	ErpMigrateProductJob::dispatchSync();
	dd("asd");
});