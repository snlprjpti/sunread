<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\UrlRewrite\Traits\HasUrlRewrite;

class ProductAttributeString extends Model
{
    use HasFactory, HasUrlRewrite;

    public static $type = "string";
    protected $fillable = [ "value" ];
    protected $table = "product_attribute_string";

    public function product_attribute()
    {
        return $this->morphOne(ProductAttribute::class, 'value');
    }
}
