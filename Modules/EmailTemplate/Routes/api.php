<?php

Route::group(["prefix" => "admin", "as" => "admin.", "middleware" => ["api", "admin", "language"]], function () {

    Route::resource("/email-templates", EmailTemplateController::class);
    Route::get("/email-templates-groups", [ \Modules\EmailTemplate\Http\Controllers\EmailTemplateGroupController::class, "index"] );
    Route::get("/email-variables", [ \Modules\EmailTemplate\Http\Controllers\EmailTemplateGroupController::class, "variable"] );

});
