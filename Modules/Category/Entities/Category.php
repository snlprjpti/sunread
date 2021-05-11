<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kalnoy\Nestedset\NodeTrait;
use Modules\Core\Entities\Channel;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\Sluggable;
use Modules\UrlRewrite\Traits\HasUrlRewrite;

class Category extends Model
{
    use NodeTrait, Sluggable, HasFactory, HasUrlRewrite;

    public static $SEARCHABLE = [ "translations.name", "slug" ];
    protected $fillable = [ "parent_id", "name", "slug", "image", "position", "description", "meta_title", "meta_description", "meta_keywords", "status" ];
    protected $with = [ "translations" ];
    public $urlRewriteType = 'category';

    public function image_url(): ?string
    {
        if (!$this->image) return null;
        return Storage::url($this->image);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_url();
    }

    public function getRootCategories(): Collection
    {
        return Category::where('parent_id', null)->get();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class);
    }

    public function getCategoryTree(): Collection
    {
        return $this->id
            ? $this::orderBy('position', 'ASC')->where('id', '!=', $this->id)->get()->toTree()
            : $this::orderBy('position', 'ASC')->get()->toTree();
    }

    public function createUrlRewrite(): string
    {
        return $this->slug;
    }

}
