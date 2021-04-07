<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = [ "name", "slug", "is_user_defined" ];

    /**
     * Get the customer for this group.
     * 
     * @return Cutomer
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}

