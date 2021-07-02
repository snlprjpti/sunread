<?php

namespace Modules\Tax\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Tax\Entities\TaxRule;

class TaxRuleRepository extends BaseRepository
{
    public function __construct(TaxRule $taxRule)
    {
        $this->model = $taxRule;
        $this->model_key = "tax-rules";
        $this->rules = [
            "customer_group_class" => "required|exists:customer_tax_groups,id",
            "product_taxable_class" => "required|exists:product_tax_groups,id",
            "name" => "required",
            "position" => "required|numeric",
            "priority" => "required|numeric",
            "subtotal" => "sometimes|nullable",
            "status" => "sometimes|boolean"
        ];
    }
}
