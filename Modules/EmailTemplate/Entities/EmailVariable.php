<?php

namespace Modules\EmailTemplate\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class EmailVariable extends Model
{
    use HasFactory;

    protected $fillable = [ "name", "value" ];
}
