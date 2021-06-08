<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeBoolean extends Model
{
    use HasFactory;

    public static $type = "boolean";
    protected $fillable = [ "value" ];
    protected $table = "product_attribute_boolean";

    
    public function product_attribute()
    {
        return $this->morphOne(ProductAttribute::class, "value");
    }
}
