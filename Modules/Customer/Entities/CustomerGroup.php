<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{

    protected $table = 'customer_groups';
    protected $fillable = ['name', 'slug', 'is_user_defined'];

    /**
     * Get the customer for this group.
     */
    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
}

