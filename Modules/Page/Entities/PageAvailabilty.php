<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class PageAvailabilty extends Model
{
    use HasFactory;

    protected $fillable = ["page_id","model_type","model_id","status"];

}
