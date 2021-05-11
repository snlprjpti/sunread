<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Entities\Store;
use Modules\UrlRewrite\Traits\HasUrlRewrite;

class CategoryTranslation extends Model
{
    use HasUrlRewrite;

    public $timestamps = false;
    protected $fillable = [ "name", "description", "meta_title", "meta_description", "meta_keywords", "store_id", "category_id" ];
    public $urlRewriteType = 'category_translation';
    protected $appends = ['url'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function createUrlRewrite(): string
    {
        return $this->store->slug.'/'.$this->name;
    }
}
