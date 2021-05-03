<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeOption extends Model
{
    public static $SEARCHABLE = [ "name" ];
    protected $fillable = [ "attribute_id", "name", "position" ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(AttributeOptionTranslation::class);
    }
}
