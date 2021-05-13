<?php

namespace Modules\Coupon\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ["code","name","description","valid_from","valid_to","flat_discount_amount","min_discount_amount", "max_discount_amount",
        "discount_percent","max_uses","single_user_uses","only_new_user","min_purchase_amount","scope_public","status"];

}
