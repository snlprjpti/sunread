<?php

namespace Modules\Country\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Region extends Model
{
    protected $fillable = [ "country_id", "code", "name" ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
