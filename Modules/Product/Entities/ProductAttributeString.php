<?php

namespace Modules\Product\Entities;

class ProductAttributeString extends ProductAttributeType
{
    public static $type = "string";
    protected $table = "product_attribute_string";
}
