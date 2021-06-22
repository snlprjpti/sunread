<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class PageConfiguration extends Model
{
    use HasFactory;

    protected $fillable =  [ "scope", "scope_id", "page_id", "title", "description", "status", "meta_title", "meta_description", "meta_keywords" ];
}
