<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeTimestamp extends Model
{
    use HasFactory;

    public static $type = "date";
    protected $fillable = [ "value" ];
    protected $table = "product_attribute_timestamp";

    public function product_attribute()
    {
        return $this->morphOne(ProductAttribute::class, "value");
    }
}
