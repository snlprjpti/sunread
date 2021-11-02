<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function order_addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function order_comments(): HasMany
    {
        return $this->hasMany(OrderComment::class);
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function order_metas(): HasMany
    {
        return $this->hasMany(OrderMeta::class);
    }

    public function order_taxes(): HasMany
    {
        return $this->hasMany(OrderTax::class);
    }
    
    public function order_transactions(): HasMany
    {
        return $this->hasMany(OrderTransactionLog::class);
    }
}
