<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id','type','filename'];
}
