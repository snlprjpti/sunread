<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\UrlRewrite\Traits\HasUrlRewrite;

class Product extends Model
{
    use HasFactory, HasUrlRewrite;

    protected $fillable = [ "parent_id", "brand_id", "attribute_group_id", "sku", "type", "status" ];
    public static $SEARCHABLE = [ "sku", "type" ];
    public $urlRewriteType = 'product';
    protected $appends = ['url'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, "parent_id");
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function attribute_group(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class)->with(["attributes"]);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function product_attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class)->with(["value"]);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy("main_image", "desc")->orderBy("position");
    }

    public function createUrlRewrite(): string
    {
        return $this->slug;
    }
}
