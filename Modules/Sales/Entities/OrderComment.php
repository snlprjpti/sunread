<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Entities\Admin;

class OrderComment extends Model
{
    use HasFactory;

    protected $fillable = ["order_id", "user_id", "is_customer_notified", "is_visible_on_storefornt", "comment"];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, "user_id");
    }
    
}
