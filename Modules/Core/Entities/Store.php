<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Traits\Sluggable;

class Store extends Model
{
    use Sluggable;

    protected $fillable = ["name","currency","locale","image","slug"];



}
