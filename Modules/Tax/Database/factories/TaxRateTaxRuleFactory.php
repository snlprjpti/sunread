<?php
namespace Modules\Tax\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Tax\Entities\TaxRate;
use Modules\Tax\Entities\TaxRateTaxRule;
use Modules\Tax\Entities\TaxRule;

class TaxRateTaxRuleFactory extends Factory
{
    protected $model = TaxRateTaxRule::class;

    public function definition(): array
    {
        $tax_rate_id = TaxRate::factory()->create()->id;
        $tax_rule_id = TaxRule::factory()->create()->id;

        return [
            "tax_rate_id" => $tax_rate_id,
            "tax_rule_id" => $tax_rule_id
        ];
    }
}

