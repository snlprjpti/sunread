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
    protected $fillable = [ "name", "image", "description", "meta_title", "meta_description", "meta_keywords", "scope", "scope_id", "category_id" ];

    protected $appends = ['url'];

    public function __construct(?array $attributes = [])
    {
        parent::__construct($attributes);
        $this->urlRewriteRoute = "admin.catalog.categories.show";
        $this->urlRewriteParameter = ["category_id"];
        $this->urlRewriteExtraFields = ["store_id"];
        $this->urlRewriteParameterKey = ["category"];
        $this->urlRewriteType = "Modules\Category\Entities\Category";

    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function getUrlRewriteRequestPathAttribute(): string
    {
       return (isset($this->store->slug) ? $this->store->slug . "/" : "") . $this->name;
    }
}
