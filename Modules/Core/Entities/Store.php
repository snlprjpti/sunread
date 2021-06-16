<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\Sluggable;

class Store extends Model
{
    use Sluggable, HasFactory;

    protected $fillable = [ "slug", "name", "currency", "locale", "image", "position", "status", "channel_id" ];

    public function getImageUrlAttribute()
    {   
        return Storage::url($this->image);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
