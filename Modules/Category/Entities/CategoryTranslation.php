<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Entities\Store;

class CategoryTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [ "name", "description", "meta_title", "meta_description", "meta_keywords", "store_id", "category_id" ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
