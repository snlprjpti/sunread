<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\Sluggable;


class Page extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = ["parent_id", "slug", "title", "description","position","status", "meta_title", "meta_description", "meta_keywords" ];

}
