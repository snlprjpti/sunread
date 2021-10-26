<?php

namespace Modules\ClubHouse\Entities;

use Modules\Core\Entities\Website;
use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubHouse extends Model
{
    use HasFactory;

    /**
     * Arrays that are Mass Assignable
     */
    protected $fillable = ["position", "website_id", "type", "status"];
    protected $with = [ "values" ];

    protected $casts = [];



    public function __construct(?array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function image_url(): ?string
    {
        if (!$this->image) return null;
        return Storage::url($this->image);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_url();
    }

    /**
     * Many to One Relation Between ClubHouse and Website
     * @return BelongsTo
     */
    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * One to Many Relation Between ClubHouse and ClubHouseValue
     * @return BelongsTo
     */
    public function values(): HasMany
    {
        return $this->hasMany(ClubHouseValue::class);
    }
}
