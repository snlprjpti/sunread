<?php

namespace Modules\Product\Repositories\StoreFront;

use DateTime;
use Exception;
use Modules\Core\Facades\PriceFormat;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\Product;
use Modules\Tax\Facades\TaxPrice;

class ProductFormatRepository extends BaseRepository
{

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function getProductInFormat(array $fetched, object $request, object $store): array
    {
        try
        {
            $today = date('Y-m-d');
            $currentDate = date('Y-m-d H:m:s', strtotime($today));

            $fetched = $this->getPriceWithFormatAndTax($fetched, $request, $store);
            $fetched = $this->getSpecialPriceWithFormatAndTax($fetched, $currentDate, $store);
            $fetched = $this->getNewProductStatus($fetched, $currentDate);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getPriceWithFormatAndTax(array $fetched, object $request, object $store): array
    {
        try
        {
            if(isset($fetched["price"])) {
                $tax_class_id = isset($fetched["tax_class"]) ? $fetched["tax_class"] : (isset($fetched["tax_class_id"]) ? $fetched["tax_class_id"] :
                null);
                $calculateTax = TaxPrice::calculate($request, $fetched["price"], $tax_class_id);
                $fetched["tax_amount"] = $calculateTax?->tax_rate_value;
                $fetched["price"] += $fetched["tax_amount"];
            }
            else {
                $fetched["tax_amount"] = 0;
                $fetched["price"] = 0;
            }
            $fetched["price_formatted"] = PriceFormat::get($fetched["price"], $store->id, "store");
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getSpecialPriceWithFormatAndTax(array $fetched, string $currentDate, object $store): array
    {
        try
        {
            if(isset($fetched["special_price"])) {
                if(isset($fetched["special_from_date"])) $fromDate = date('Y-m-d H:m:s', strtotime($fetched["special_from_date"]));
                if(isset($fetched["special_to_date"])) $toDate = date('Y-m-d H:m:s', strtotime($fetched["special_to_date"])); 
                if(!isset($fromDate) && !isset($toDate)) $fetched["special_price_formatted"] = PriceFormat::get($fetched["special_price"], $store->id, "store");
                else $fetched["special_price_formatted"] = (($currentDate >= $fromDate) && ($currentDate <= $toDate)) ? PriceFormat::get($fetched["special_price"], $store->id, "store") : null;
            }
            else {
                $fetched["special_price"] = null;
                $fetched["special_price_formatted"] = null;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getNewProductStatus(array $fetched, string $currentDate): array
    {
        try
        {         
            if(isset($fetched["new_from_date"]) && isset($fetched["new_to_date"])) { 
                if(isset($fetched["new_from_date"])) $fromNewDate = date('Y-m-d H:m:s', strtotime($fetched["new_from_date"]));
                if(isset($fetched["new_to_date"])) $toNewDate = date('Y-m-d H:m:s', strtotime($fetched["new_to_date"])); 
                if(isset($fromNewDate) && isset($toNewDate)) $fetched["is_new_product"] = (($currentDate >= $fromNewDate) && ($currentDate <= $toNewDate)) ? 1 : 0;
                unset($fetched["new_from_date"], $fetched["new_to_date"]);
            }
            else $fetched["is_new_product"] = 0;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }
}
