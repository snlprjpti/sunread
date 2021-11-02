<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderComment extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
}
