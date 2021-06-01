<?php

Route::group(["middleware" => ["api"]], function () {
    //ADMIN ATTRIBUTE ROUTES
    Route::group(["prefix" => "admin", "middleware" => ["admin", "language"], "as" => "admin."], function () {
        // Activities Routes
        Route::delete("activities/bulk", [\Modules\Core\Http\Controllers\ActivityLogController::class, "bulkDelete"])->name("activities.bulk-delete");
        Route::resource("activities", ActivityLogController::class)->only(["index", "show", "destroy"]);

        // Locale Routes
        Route::resource("locales", LocaleController::class)->except(["create", "edit"]);

        // Store Routes
        Route::resource("stores", StoreController::class)->except(["create","edit"]);

        // Currency Routes
        Route::resource("currencies", CurrencyController::class)->except(["create", "edit"]);

        // Exchange Rates Routes
        Route::resource("exchange_rates", ExchangeRateController::class)->except(["create", "edit"]);

        // Channels Routes
        Route::resource("channels", ChannelController::class)->except(["create", "edit"]);
       
        // Websites Routes
        Route::get("websites/{website_id}/relationships", [\Modules\Core\Http\Controllers\WebsiteController::class, "relationships"])->name("websites.relationships");
        Route::resource("websites", WebsiteController::class)->except(["create", "edit"]);

        // Configurations Routes
        Route::resource("configurations", ConfigurationController::class)->except(["create", "edit"]);
    });
});
