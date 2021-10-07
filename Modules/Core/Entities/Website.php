<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Facades\SiteConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Website extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "code", "hostname", "name", "description" ];
    protected $fillable = [ "code", "hostname", "name", "description", "position", "status", "default_channel_id" ];
    protected $with = [ "channels" ];

    public function default_channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, "default_channel_id");
    }

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
