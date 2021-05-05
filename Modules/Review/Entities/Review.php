<?php

namespace Modules\Review\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\HasFactory;
use Modules\Customer\Entities\Customer;
use Modules\Product\Entities\Product;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [ "customer_id", "product_id", "rating", "title", "description", "status" ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }

}