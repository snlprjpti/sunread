<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [ "name", "image", "description" ];

}
