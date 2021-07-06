<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\HasTranslation;
use Modules\Core\Traits\Sluggable;
use Modules\Page\Traits\HasPageConfiguration;

class Page extends Model
{
    use HasFactory, Sluggable;
    use HasTranslation, HasPageConfiguration {
        HasTranslation::getAttribute as getAttributeTranslate;
        HasPageConfiguration::getAttribute as getAttributeConfig;
    }

    public function getAttribute($name)
    {
        return $this->getAttributeTranslate($name) ?? $this->getAttributeConfig($name);
    }

    protected $fillable = [ "parent_id", "slug", "title", "description", "position", "status", "meta_title", "meta_description", "meta_keywords" ];
    protected $with = [ "translations" ];

    public $translatedAttributes = ["title", "description", "meta_title", "meta_description", "meta_keywords"];
    public $translatedModels = [ PageTranslation::class, "page_id" ];

    public $configAttributes = ["title", "description", "status", "meta_title", "meta_description", "meta_keywords"];
    public $configModels = [ PageConfiguration::class];

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, "parent_id");
    }

    public function scopePublished(object $query): object
    {
        return $query->whereStatus(1);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PageImage::class);
    }
}
