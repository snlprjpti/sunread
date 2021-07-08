<?php
return [
    "base_image" => [
        "module" => "Modules\Product\Entities\ProductImage",
        "options" => 0,
        "pluck" => []
    ],
    "small_image" => [
        "module" => "Modules\Product\Entities\ProductImage",
        "options" => 0,
        "pluck" => []
    ],
    "thumbnail_image" => [
        "module" => "Modules\Product\Entities\ProductImage",
        "options" => 0,
        "pluck" => []
    ],
    "sku" => [
        "module" => "Modules\Product\Entities\Product",
        "options" => 0,
        "pluck" => []
    ],
    "status" => [
        "module" => "Modules\Product\Entities\Product",
        "options" => 0,
        "pluck" => []
    ],
    "tax_class_id" => [
        "module" => "Modules\Tax\Entities\ProductTaxGroup",
        "options" => 1,
        "pluck" => [ "id", "name" ]
    ]
];