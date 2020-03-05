<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = ['type', 'attribute_family_id', 'sku', 'parent_id'];
}
