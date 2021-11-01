<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTaxItem extends Model
{
    use HasFactory;

    protected $fillable = [];
    
}
