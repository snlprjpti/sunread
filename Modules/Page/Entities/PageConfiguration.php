<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class PageConfiguration extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "scope", "path", "value" ];
    protected $fillable =  [ "scope", "scope_id", "path", "value" ];
    protected $casts = [ "value" => "array" ];

}
