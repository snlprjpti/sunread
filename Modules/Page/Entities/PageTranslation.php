<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Entities\Store;
use Modules\Core\Traits\HasFactory;

class PageTranslation extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ["store_id", "page_id", "title","description", "meta_title", "meta_description", "meta_keywords" ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

}
