<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\Sluggable;

class AttributeGroup extends Model
{
    use Sluggable;

    public static $SEARCHABLE = [ "name", "slug" ];
    protected $fillable = [ "attribute_family_id", "name", "slug", "position", "is_user_defined" ];

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    public function attribute_family(): BelongsTo
    {
        return $this->belongsTo(AttributeFamily::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(AttributeGroupTranslation::class);
    }
}
