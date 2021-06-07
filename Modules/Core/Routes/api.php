<?php

Route::group(["middleware" => ["api"]], function () {
    // Visitor channel stores
    Route::get("channel/{id}/stores", [Modules\Core\Http\Controllers\Visitors\ChannelController::class, "stores"])->name("channel-stores.index");

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
        Route::put("/currencies/{currency_id}/update-status", [\Modules\Core\Http\Controllers\CurrencyController::class, "updateStatus"])->name("currencies.status");
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
