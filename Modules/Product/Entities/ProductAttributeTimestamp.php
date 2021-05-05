<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeTimestamp extends Model
{
    use HasFactory;

    protected $fillable = [ "value" ];
    protected $table = "product_attribute_timestamp";
}
