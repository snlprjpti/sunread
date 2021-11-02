<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTaxItem extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function order_tax(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
}
