<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Product\IndexConfigurator\ProductIndexConfigurator;
use Modules\Product\Traits\ElasticSearch\ElasticSearchFormat;
use ScoutElastic\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;
    // use ElasticSearchFormat;

    protected $fillable = [ "parent_id", "website_id", "brand_id", "attribute_set_id", "sku", "type", "status" ];
    public static $SEARCHABLE = [ "sku", "type" ];

    protected $indexConfigurator = ProductIndexConfigurator::class;

    protected $searchRules = [
        //
    ];
    
    protected $mapping;

    public function __construct(?array $attributes = [])
    {
        parent::__construct($attributes);
        $this->mapping = config('mapping');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, "parent_id");
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class);
    }

    public function attribute_group(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class)->with(["attributes"]);
    }

    public function attribute_set(): BelongsTo
    {
        return $this->belongsTo(AttributeSet::class);
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
    
    public function toSearchableArray()
    {
        return $this->toArray();
        // return $this->documentDataStructure();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function catalog_inventories(): HasMany
    {
        return $this->hasMany(CatalogInventory::class);
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function attribute_configurable_products(): HasMany
    {
        return $this->hasMany(AttributeConfigurableProduct::class);
    }
}
