<?php

namespace Modules\EmailTemplate\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [ "name", "slug", "subject", "content", "style" ];
}
