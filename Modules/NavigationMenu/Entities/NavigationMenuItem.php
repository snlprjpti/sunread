<?php

namespace Modules\NavigationMenu\Entities;

use Modules\Core\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\NavigationMenu\Traits\HasScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationMenuItem extends Model
{
    use HasFactory, HasScope;

    /**
     * Arrays that are Mass Assignable
     */
    protected $fillable = ["position", "website_id", "type", "status"];

    // Append data with Values [ClubHouseValue]
    protected $with = [ "values" ];

    // Searchable
    public static $SEARCHABLE = [];

    protected $casts = [];

    /**
     * Get Image URL
     */
    public function image_url(): ?string
    {
        if (!$this->image) return null;
        return Storage::url($this->image);
    }

    /**
     * Get Image URL Attribute
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_url();
    }

    /**
     * One to Many Relation Between NavigationMenuItem and NavigationMenuItemValue
     */
    public function values(): HasMany
    {
        return $this->hasMany(NavigationMenuItemValue::class);
    }

    /**
     * Many to One Relation Between NavigationMenuItem and NavigationMenu
     */
    public function navigationMenu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class);
    }
}
