<?php

namespace Modules\Attribute\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\Sluggable;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeFamily extends Model
{
    use Sluggable;

    public static $SEARCHABLE = [ "name" ];
    protected $fillable = [ "name", "slug", "status" ];

    public function attributeGroups(): HasMany
    {
        return $this->hasMany(AttributeGroup::class, "attribute_family_id");
    }

    public function custom_attributes(): Builder
    {
        return DB::table("attributes")
            ->join("attribute_groups", "attributes.attribute_group_id", "=", "attribute_groups.id")
            ->join("attribute_families", "attribute_groups.attribute_family_id", "=", "attribute_families.id")
            ->where("attribute_families.id", $this->id)
            ->select("attributes.*");
    }
}
