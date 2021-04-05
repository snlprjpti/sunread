<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\Sluggable;

class AttributeFamily extends Model
{
    use Sluggable;

    public static $SEARCHABLE = [ "name" ];
    public $timestamps= false;
    protected $fillable = [ "name", "slug" ];

    /**
     * Attribute Groups relationship
     * 
     * @return AttributeGroup
     */
    public function attributeGroups()
    {
        return $this->hasMany(AttributeGroup::class, "attribute_family_id");
    }

    /**
     * Custom Attributes
     * 
     * @return Collection
     */
    public function custom_attributes()
    {
        return DB::table("attributes")
            ->join("attribute_groups", "attributes.attribute_group_id", "=", "attribute_groups.id")
            ->join("attribute_families", "attribute_groups.attribute_family_id", "=", "attribute_families.id")
            ->where("attribute_families.id", $this->id)
            ->select("attributes.*");
    }
}
