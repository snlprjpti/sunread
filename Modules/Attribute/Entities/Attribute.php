<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\HasFactory;
use Modules\Core\Traits\Sluggable;

class Attribute extends Model
{
    use Sluggable, HasFactory;

    public static $SEARCHABLE = [ "name", "type" ];
    protected $fillable = [ "attribute_group_id", "slug", "name", "type", "position", "validation", "is_required", "is_unique", "is_filterable", "is_searchable", "is_user_defined", "is_visible_on_front" ];

    public function attribute_group(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class);
    }

    public function attribute_options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(AttributeTranslation::class);
    }

    public function generateValidation(): array
    {
        $validation = $this->is_required ? ["required"] : ["sometimes", "nullable"];
        return array_merge($validation, [config("validation.$this->type")]);
    }

    public function getValidationAttribute(): ?string
    {
        return $this->validation ?? implode("|", $this->generateValidation());
    }
}
