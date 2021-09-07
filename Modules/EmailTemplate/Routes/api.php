<?php

Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api", "admin", "language"]], function () {

    Route::get("/email-templates/variables", [\Modules\EmailTemplate\Http\Controllers\EmailTemplateController::class, "getVariables"]);
    Route::resource("/email-templates", EmailTemplateController::class);
});
