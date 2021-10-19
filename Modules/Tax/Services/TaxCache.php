<?php

namespace Modules\Tax\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Tax\Entities\CustomerTaxGroup;
use Modules\Tax\Entities\ProductTaxGroup;
use Modules\Tax\Entities\TaxRate;
use Modules\Tax\Entities\TaxRule;

class TaxCache 
{

    public function setProductTaxGroup(): void
    {
        try
        {
            Cache::forget("product_tax_group");

            Cache::remember("product_tax_group", Carbon::now()->addDays(2), function () {
                return ProductTaxGroup::with(["tax_rules.tax_rates.country"])->get();
            });
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function setCustomerTaxGroup()
    {
        # code...
    }
    
    public function productTaxGroup(): mixed
    {
        try
        {
            if (!Cache::has("product_tax_group")) {
                Cache::remember("product_tax_group", Carbon::now()->addDays(2), function () {
                    return ProductTaxGroup::with(["tax_rules.tax_rates.country"])->get();
                });
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return Cache::get("product_tax_group");
    }

    public function customerTaxGroup(): mixed
    {
        try
        {
            if (!Cache::has("customer_tax_group")) {
                Cache::remember("customer_tax_group", Carbon::now()->addDays(2), function () {
                    return CustomerTaxGroup::with(["tax_rules.tax_rates.country"])->get();
                });
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return Cache::get("customer_tax_group");
    }

    public function taxRate(): mixed
    {
        try
        {
            if (!Cache::has("tax_rate")) {
                Cache::remember("tax_rate", Carbon::now()->addDays(2), function () {
                    return TaxRate::with(["country", "region", "tax_rules"])->get();
                });
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return Cache::get("tax_rate");
    }

    public function taxRule(): mixed
    {
        try
        {
            if (!Cache::has("tax_rule")) {
                Cache::remember("tax_rule", Carbon::now()->addDays(2), function () {
                    return TaxRule::with(["tax_rates.country", "product_tax_groups", "customer_tax_groups"])->get();
                });
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return Cache::get("tax_rule");
    }

}
