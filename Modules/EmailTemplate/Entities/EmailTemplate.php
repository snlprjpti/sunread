<?php

namespace Modules\EmailTemplate\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\Sluggable;

class EmailTemplate extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [ "name", "slug", "subject", "content", "style" ];
}
