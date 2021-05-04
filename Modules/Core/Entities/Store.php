<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\Sluggable;

class Store extends Model
{
    use Sluggable, HasFactory;

    protected $fillable = [ "slug", "name", "currency", "locale", "image" ];

    public function getImageUrlAttribute()
    {   
        return Storage::url($this->image);
    }
}
