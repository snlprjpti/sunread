<?php

namespace Modules\NavigationMenu\Entities;

use Modules\Core\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\NavigationMenu\Traits\HasScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class NavigationMenu.
 *
 * @package Modules\NavigationMenu\Entities
 *
 * @property integer id
 * @property string title
 * @property string slug
 * @property string location
 * @property integer status
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class NavigationMenu extends Model
{
    use HasFactory, Sluggable, HasScope;

    /**
     * Arrays that are Mass Assignable
     */
    protected $fillable = ['title', 'slug', 'status', 'location'];

    // Searchable
    public static $SEARCHABLE = [];

    protected $casts = [];

    /**
     * One to Many Relation Between NavigationMenu and NavigationMenuItem
     */
    public function navigationMenuItems(): HasMany
    {
        return $this->hasMany(NavigationMenuItem::class);
    }
}
