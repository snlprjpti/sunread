<?php

namespace Modules\Tax\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Tax\Entities\TaxRateTaxRule;

class TaxRateTaxRateRepository extends BaseRepository
{
    public function __construct(TaxRateTaxRule $taxRateTaxRule)
    {
        $this->model = $taxRateTaxRule;
        $this->model_key = "tax-rate-tax-rules";
        $this->rules = [
            "tax_rate_id" => "required|exists:tax_rates,id",
            "tax_rule_id" => "required|exists:tax_rules,id"
        ];
    }
}
