<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ["website_id", "store_id", "customer_id", "store_name", "is_guest", "billing_address_id", "shipping_address_id", "shipping_method", "shipping_method_label", "payment_method", "payment_method_label", "currency_code", "coupon_code", "discount_amount", "discount_amount_tax", "shipping_amount", "shipping_amount_tax", "sub_total", "sub_total_tax_amount", "tax_amount", "grand_total", "weight", "total_items_ordered", "total_qty_ordered", "customer_email", "customer_first_name", "customer_middle_name", "customer_last_name", "customer_phone", "customer_taxvat", "customer_ip_address", "status", "external_erp_id"];

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
