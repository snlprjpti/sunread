<?php

Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api", "admin", "language"]], function () {

    Route::resource("/email-templates", EmailTemplateController::class);
});
