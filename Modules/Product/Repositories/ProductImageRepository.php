<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\ProductImage;

class ProductImageRepository extends BaseRepository
{
    public function __construct(ProductImage $productImage)
    {
        $this->model = $productImage;
        $this->model_key = "catalog.product.images";
        $this->rules = [
            "product_id" => "required|exists:products,id",
            "image" => "required|mimes:bmp,jpeg,jpg,png",
            "position" => "sometimes|numeric",
            "main_image" => "sometimes|boolean"
        ];
    }
}
