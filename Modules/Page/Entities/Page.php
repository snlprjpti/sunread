<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\HasTranslation;
use Modules\Core\Traits\Sluggable;

class Page extends Model
{
    use HasFactory, Sluggable, HasTranslation;

    protected $fillable = [ "parent_id", "slug", "title", "description", "position", "status", "meta_title", "meta_description", "meta_keywords" ];
    protected $with = [ "translations" ];

    public $translatedAttributes = ["title", "description", "meta_title", "meta_description", "meta_keywords"];
    public $translatedModels = [ PageTranslation::class, "page_id" ];

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, "parent_id");
    }

    public function scopePublished($query): object
    {
        return $query->whereStatus(1);
    }
}
