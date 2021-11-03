<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;

class OrderItemTableSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            "website_id" => Website::first()?->id,
            "store_id" => Store::first()?->id,
            "product_id" => 1,
            "order_id" => 1,
            "product_options" => json_decode([
                "image_url" => url("product.png"),
                "configurable_attributes" => [
                    "size" => [
                        "attribute_id" => 1,
                        "label" => "size",
                        "name" => "Size",
                        "value" => "L" 
                    ],
                    "color" => [
                        "attribute_id" => 2,
                        "label" => "color",
                        "name" => "Color",
                        "value" => "L" 
                    ]
                ]
            ])
        ]
    }
}
