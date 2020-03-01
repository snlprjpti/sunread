<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    protected $fillable = [
        'code', 'name', 'direction'
    ];
}
