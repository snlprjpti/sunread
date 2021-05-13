<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeText extends Model
{
    use HasFactory;

    public static $type = "text";
    protected $fillable = [ "value" ];
    protected $table = "product_attribute_text";
}
