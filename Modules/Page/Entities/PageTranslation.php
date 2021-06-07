<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class PageTranslation extends Model
{
    use HasFactory;

    protected $fillable = ["store_id", "page_id", "title","description", "meta_title", "meta_description", "meta_keywords" ];


}
