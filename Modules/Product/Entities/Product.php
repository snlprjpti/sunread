<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [ "parent_id", "brand_id", "attribute_group_id", "sku", "type", "status" ];
    public static $SEARCHABLE = [ "sku", "type" ];

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
        return $this->belongsTo(AttributeGroup::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
