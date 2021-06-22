<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\UrlRewrite\Traits\HasUrlRewrite;

class CategoryValue extends Model
{
    use HasUrlRewrite;

    public $timestamps = false;
    protected $fillable = [ "scope", "scope_id", "name", "image", "description", "meta_title", "meta_description", "meta_keywords", "category_id", "status", "include_in_menu" ];

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

    public function getUrlRewriteRequestPathAttribute(): string
    {
       return (isset($this->store->slug) ? $this->store->slug . "/" : "") . $this->name;
    }
}
