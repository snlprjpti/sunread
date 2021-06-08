<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\Sluggable;

class Page extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [ "parent_id", "slug", "title", "description", "position", "status", "meta_title", "meta_description", "meta_keywords" ];
    protected $with = [ "translations" ];

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, "parent_id");
    }
}
