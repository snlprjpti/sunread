<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class CacheManagement extends Model
{
    use HasFactory;

    protected $fillable = [ "name", "slug", "description", "tag", "key" ];
}
