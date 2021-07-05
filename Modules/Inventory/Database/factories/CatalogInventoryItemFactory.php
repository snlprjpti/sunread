<?php
namespace Modules\Inventory\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Product\Entities\Product;

class CatalogInventoryItemFactory extends Factory
{
    protected $model = \Modules\Inventory\Entities\CatalogInventoryItem::class;

    public function definition(): array
    {
        return [
            "product_id" => Product::factory()->create()->id,
            "event" => "Default Adjustment",
            "adjusted_by" => 1,
            "quantity" => 5,
            "adjustment_type" => "addition",
            "created_at" => now(),
            "updated_at" => now()
        ];
    }
}

