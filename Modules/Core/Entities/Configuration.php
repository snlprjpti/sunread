<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class Configuration extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "scope", "path", "value" ];
    protected $fillable =  [ "scope", "scope_id", "path", "value", "use_default_value" ];
    protected $casts = [ "value" => "array" ];

}
