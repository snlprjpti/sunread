<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Sluggable;

class CustomerGroup extends Model
{
    use Sluggable;

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

