<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [ "attribute_id", "channel_id", "product_id", "store_id", "value_type", "value_id" ];
    public $timestamps = false;

    public function value(): MorphTo
    {
        return $this->morphTo();
    }
}
