<?php

namespace Modules\Product\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\Product;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->model_key = "catalog.products";
        $this->rules = [
            "parent_id" => "sometimes|nullable|exists:products,id",
            "brand_id" => "sometimes|nullable|exists:brands,id",
            "attribute_group_id" => "required|exists:attribute_groups,id",
            "sku" => "required|unique:products,sku",
            "type" => "required|in:simple,configurable",
            "status" => "sometimes|boolean"
        ];
    }
}
