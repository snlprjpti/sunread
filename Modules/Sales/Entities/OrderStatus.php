<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    use HasFactory;

    protected $fillable = ["name", "slug"];

    public function order_status_state(): BelongsTo
    {
        return $this->belongsTo(OrderStatusState::class, "status", "slug");
    }
    
}
