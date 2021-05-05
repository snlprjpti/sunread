<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\Sluggable;

class CustomerGroup extends Model
{
    use Sluggable, HasFactory;

    protected $fillable = [ "name", "slug", "is_user_defined" ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}

