<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = ["product_id","position","path","main_image","small_image","thumbnail"];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
