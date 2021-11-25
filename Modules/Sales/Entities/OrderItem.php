<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ["website_id", "store_id", "product_id", "order_id", "product_options", "product_type", "sku", "name", "weight", "qty", "cost", "price", "price_incl_tax", "coupon_code", "discount_amount", "discount_percent", "discount_amount_tax", "tax_amount", "tax_percent", "row_total", "row_total_incl_tax", "row_weight"];

    protected $casts = ['product_options' => 'array']; 

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
}
