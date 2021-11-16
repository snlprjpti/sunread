<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;
use Modules\Sales\Entities\Order;

class OrderItemTableSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                "website_id" => Website::first()?->id,
                "store_id" => Store::first()?->id,
                "product_id" => 1,
                "order_id" => Order::first()?->id,
                "product_options" => json_encode([
                    "product_options" => [
                       "attributes" => [
                            [
                                "attribute_id" => 1,
                                "label" => "size",
                                "name" => "Size",
                                "value" => "L" 
                            ],
                            [
                                "attribute_id" => 2,
                                "label" => "color",
                                "name" => "Color",
                                "value" => "L" 
                            ],
                        ],
                        "image_url" => url("product.png"), 
                    ],
                ]),
                "product_type" => "simple",
                "sku" => "dell-laptop",
                "name" => "Dell laptop Bag",
                "qty" => 10.00,
                "cost" => 10.00,
                "price" => 10.00,
                "price_incl_tax" => 11.3,
                "coupon_code" => "new-user-discount",
                "discount_percent" => 5.00,
                "discount_amount" => 0.5,
                "discount_amount_tax" => 0.565,
                "tax_amount" => 1.3,
                "tax_percent" => 13,
                "row_total" => 100,
                "row_total_incl_tax" => 100.735,
                "row_weight" => 150.1,
            ],
            [
                "website_id" => Website::first()?->id,
                "store_id" => Store::first()?->id,
                "product_id" => 1,
                "order_id" => 1,
                "product_options" => json_encode([
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
                        ],
                        "image_url" => url("product.png"), 
                    ],
                ]),
                "product_type" => "simple",
                "sku" => "dell-laptop",
                "name" => "Dell laptop Bag",
                "qty" => 10.00,
                "cost" => 10.00,
                "price" => 10.00,
                "price_incl_tax" => 11.3,
                "coupon_code" => "new-user-discount",
                "discount_percent" => 5.00,
                "discount_amount" => 0.5,
                "discount_amount_tax" => 0.565,
                "tax_amount" => 1.3,
                "tax_percent" => 13,
                "row_total" => 100,
                "row_total_incl_tax" => 100.735,
                "row_weight" => 150.1,
            ]
        ];

        DB::table("order_items")->insert($data);
    }
}
