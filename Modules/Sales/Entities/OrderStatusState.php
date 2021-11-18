<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusState extends Model
{
    use HasFactory;

    public $preventsLazyLoading = false;

    protected $fillable = ["status", "state", "is_default", "position"];
    
}
