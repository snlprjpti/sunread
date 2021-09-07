<?php

Route::group(["middleware" => ["api"]], function () {
    // ADMIN EMAIL TEMPLATE ROUTES
    Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api", "admin", "language"]], function () {

        Route::resource("/email-template", EmailTemplateController::class);
    });
});
