<?php

Route::group(["middleware" => ["api"]], function () {
    // ADMIN TAX RATE ROUTES
    Route::group(["prefix" => "admin/taxes", "as" => "admin.taxes.", "middleware" => ["admin", "language"]], function () {
        Route::resource("tax-rates", TaxRateController::class)->except(["create", "edit"]);
        Route::resource("customer-tax-groups", CustomerTaxGroupController::class)->except(["create", "edit"]);
        Route::resource("product-tax-groups", ProductTaxGroupController::class)->except(["create", "edit"]);

        Route::put("/tax-rules/{tax_rule_id}/status", [\Modules\Tax\Http\Controllers\TaxRuleController::class, "updateStatus"])->name("status");
        Route::resource('tax-rules', TaxRuleController::class)->except(['create', 'edit']);
    });
});
