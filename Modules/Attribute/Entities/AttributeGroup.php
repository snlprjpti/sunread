<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\HasTranslation;
use Modules\Core\Traits\Sluggable;

class AttributeGroup extends Model
{
    use Sluggable, HasFactory, HasTranslation;

    public static $SEARCHABLE = [ "name", "slug" ];
    protected $fillable = [ "attribute_family_id", "name", "slug", "position", "is_user_defined" ];

    public $translatedAttributes = ["name"];
    public $translatedModels = [ AttributeGroupTranslation::class, "attribute_group_id" ];
    
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
