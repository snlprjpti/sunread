<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = [
        'customer_id','address1' ,'address2','country','state','city','postcode','phone','default_address'
    ];
}
