<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\HasFactory;


class Website extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "code", "hostname", "name", "description" ];
    protected $fillable = [ "code", "hostname", "name", "description", "position" ];

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class, 'website_id');
    }
    
}
