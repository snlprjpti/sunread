<?php

namespace Modules\Tax\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTaxGroup extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "name", "description" ];
    protected $fillable = [ "name", "description" ];
}
