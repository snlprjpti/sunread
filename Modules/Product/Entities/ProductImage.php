<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = ["product_id","position","path","main_image","small_image","thumbnail"];

}
