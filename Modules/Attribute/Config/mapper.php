<?php
return [
    "base_image" => [
        "module" => "Modules\Product\Entities\ProductImage",
        "options" => 0,
        "pluck" => [],
        "attribute" => "path",
        "field" => "product_id"
    ],
    "small_image" => [
        "module" => "Modules\Product\Entities\ProductImage",
        "options" => 0,
        "pluck" => [],
        "attribute" => "path",
        "field" => "product_id"
    ],
    "thumbnail_image" => [
        "module" => "Modules\Product\Entities\ProductImage",
        "options" => 0,
        "pluck" => [],
        "attribute" => "path",
        "field" => "product_id"
    ],
    "sku" => [
        "module" => "Modules\Product\Entities\Product",
        "options" => 0,
        "pluck" => [],
        "attribute" => "sku",
        "field" => "id"
    ],
    "status" => [
        "module" => "Modules\Product\Entities\Product",
        "options" => 0,
        "pluck" => [],
        "attribute" => "status",
        "field" => "id"
    ],
    "tax_class_id" => [
        "module" => "Modules\Tax\Entities\ProductTaxGroup",
        "options" => 1,
        "pluck" => [ "id", "name" ]
    ],
    "quantity_and_stock_status" => [
        "module" => "Modules\Inventory\Entities\CatalogInventory",
        "options" => 0,
        "pluck" => [],
        "attribute" => "is_in_stock",
        "field" => "product_id"
    ],
    "category_ids" => [
        "module" => "Modules\Inventory\Entities\CatalogInventory",
        "options" => 0,
        "pluck" => [],
        "attribute" => "is_in_stock",
        "field" => "product_id"
    ]
];