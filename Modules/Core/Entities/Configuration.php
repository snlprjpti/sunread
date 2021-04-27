<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Configuration extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "scope", "path", "value" ];
    protected $fillable =  [ "scope", "scope_id", "path", "value" ];
    
   
}
