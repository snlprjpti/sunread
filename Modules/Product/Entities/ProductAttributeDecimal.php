<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeDecimal extends Model
{
    use HasFactory;

    public static $type = "decimal";
    protected $fillable = [ "value" ];
    protected $table = "product_attribute_decimal";

    public function product_attribute()
    {
        return $this->morphOne(ProductAttribute::class, "value");
    }
}
