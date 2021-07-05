<?php
namespace Modules\Tax\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Tax\Entities\CustomerTaxGroup;
use Modules\Tax\Entities\ProductTaxGroup;
use Modules\Tax\Entities\TaxRule;

class TaxRuleFactory extends Factory
{
    protected $model = TaxRule::class;

    public function definition(): array
    {
         $customer_tax_id = CustomerTaxGroup::factory()->create()->id;
         $product_tax_id = ProductTaxGroup::factory()->create()->id;

        return [
            "customer_group_class" => $customer_tax_id,
            "product_taxable_class" => $product_tax_id,
            "name" => $this->faker->name(),
            "position" => $this->faker->randomDigit(),
            "priority" => $this->faker->randomDigit(),
            "subtotal" => $this->faker->randomFloat(2, min: 5, max: 20),
            "status" => 1
        ];
    }
}

