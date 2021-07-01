<?php

namespace Modules\Tax\Repositories;

use Modules\Tax\Entities\TaxRate;
use Modules\Core\Repositories\BaseRepository;

class TaxRateRepository extends BaseRepository
{
    public function __construct(TaxRate $TaxRate)
    {
        $this->model = $TaxRate;
        $this->model_key = "tax-rates";

        $this->rules = [
            "country_id" => "sometimes|nullable", // "sometimes|nullable|exists:countries,id"
            "region_id" => "sometimes|nullable", // "sometimes|nullable|exists:regions,id"
            "identifier" => "required|unique:tax_rates,identifier",
            "use_zip_range" => "required|boolean",
            "zip_code" => "sometimes|nullable",
            "postal_code_from" => "sometimes|nullable",
            "postal_code_to" => "sometimes|nullable",
            "tax_rate" => "required|decimal"
        ];
    }
}
