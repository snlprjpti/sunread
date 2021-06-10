<?php

Route::group(["middleware" => ["api"]], function () {
    // Visitor channel stores
    Route::get("channels/{id}/stores", [Modules\Core\Http\Controllers\Visitors\ChannelController::class, "stores"])->name("channels.stores.index");

    //ADMIN ATTRIBUTE ROUTES
    Route::group(["prefix" => "admin", "middleware" => ["admin", "language"], "as" => "admin."], function () {
        // Activities Routes
        Route::delete("activities/bulk", [\Modules\Core\Http\Controllers\ActivityLogController::class, "bulkDelete"])->name("activities.bulk-delete");
        Route::resource("activities", ActivityLogController::class)->only(["index", "show", "destroy"]);

        // Locale Routes
        Route::resource("locales", LocaleController::class)->except(["create", "edit"]);

        // Store Routes
        Route::put("stores/{store_id}/status", [\Modules\Core\Http\Controllers\StoreController::class, "updateStatus"])->name("stores.status");
        Route::resource("stores", StoreController::class)->except(["create","edit"]);

        // Currency Routes
        Route::put("/currencies/{currency_id}/status", [\Modules\Core\Http\Controllers\CurrencyController::class, "updateStatus"])->name("currencies.status");
        Route::resource("currencies", CurrencyController::class)->except(["create", "edit"]);

        // Exchange Rates Routes
        Route::resource("exchange_rates", ExchangeRateController::class)->except(["create", "edit"]);

        // Channels Routes
        Route::put("channels/{channel_id}/status", [\Modules\Core\Http\Controllers\ChannelController::class, "updateStatus"])->name("channels.status");
        Route::resource("channels", ChannelController::class)->except(["create", "edit"]);

        // Websites Routes
        Route::put("websites/{website_id}/status", [\Modules\Core\Http\Controllers\WebsiteController::class, "updateStatus"])->name("websites.status");
        Route::get("websites/{website_id}/relationships", [\Modules\Core\Http\Controllers\WebsiteController::class, "relationships"])->name("websites.relationships");
        Route::resource("websites", WebsiteController::class)->except(["create", "edit"]);

        // Configurations Routes
        Route::resource("configurations", ConfigurationController::class)->except(["create", "edit"]);
    });
});
