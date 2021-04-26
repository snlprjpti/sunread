<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;


class Website extends Model
{
    public static $SEARCHABLE = [ "code", "hostname", "name", "description"];
    protected $fillable = [ "code", "hostname", "name", "description",];
    
}
