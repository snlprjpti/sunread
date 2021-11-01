<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [];
    
}
