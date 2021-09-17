<?php

Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api", "admin", "language"]], function () {

    Route::group(["prefix" => "email-templates", "as" => "email-templates."], function () {

        Route::get("/groups", [\Modules\EmailTemplate\Http\Controllers\EmailTemplateGroupController::class, "index"])->name("groups");
        Route::get("/variables", [\Modules\EmailTemplate\Http\Controllers\EmailTemplateGroupController::class, "variable"])->name("variables");
        Route::resource("/", EmailTemplateController::class);

    });
});
