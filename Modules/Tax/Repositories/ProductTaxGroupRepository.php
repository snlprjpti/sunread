<?php

namespace Modules\Tax\Repositories;

use Modules\Tax\Entities\ProductTaxGroup;
use Modules\Core\Repositories\BaseRepository;

class ProductTaxGroupRepository extends BaseRepository
{
    public function __construct(ProductTaxGroup $productTaxGroup)
    {
        $this->model = $productTaxGroup;
        $this->model_key = "product-tax-groups";

        $this->rules = [
            "name" => "required",
            "description" => "required"
        ];
    }
}
