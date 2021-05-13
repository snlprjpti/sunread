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

    protected $appends = ['url'];

    public function __construct(?array $attributes = [])
    {
        parent::__construct($attributes);
        $this->urlRewriteRoute = "admin.catalog.categories.categories.show";
        $this->urlRewriteParameter = ["category_id"];
        $this->urlRewriteExtraFields = ["store_id"];
        $this->urlRewriteParameterKey = ["category"];
        
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function createUrlRewrite(): string
    {
        return $this->store->slug.'/'.$this->name;
    }
}
