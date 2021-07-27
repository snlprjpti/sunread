<?php

namespace Modules\Core\Entities;

use Modules\Core\Traits\HasFactory;
use Modules\Core\Facades\SiteConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Website extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "code", "hostname", "name", "description" ];
    protected $fillable = [ "code", "hostname", "name", "description", "position", "status" ];
    protected $with = [ "channels" ];

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class, 'website_id');
    }

    public function getStoresCountAttribute(): int
    {
        return $this->channels->map(function($channel) {
            return (int) $channel->stores->count();
        })->sum();
    }
}
