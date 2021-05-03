<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class CustomerAddress extends Model
{
    use HasFactory;
    protected $fillable = [ "customer_id", "address1", "address2", "country", "state", "city", "postcode", "phone", "default_address","name" ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
