<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Sluggable;

class Attribute extends Model
{
    use Sluggable;

    public $translatedAttributes = ["name"];
    public static $SEARCHABLE = [ "name", "type" ];
    protected $fillable = [ "slug", "name", "type", "position", "is_required", "is_unique", "validation", "is_filterable", "is_visible_on_front", "is_user_defined", "use_in_flat", "create_at", "updated_at", "attribute_group_id" ];

    /**
     * Attribute options relationship
     * 
     * @return AttributeOption
     */
    public function attributeOptions()
    {
        return $this->hasMany(AttributeOption::class, "attribute_id");
    }

    /**
     * Translations relationship
     * 
     * @return AttributeTranslation
     */
    public function translations()
    {
        return $this->hasMany(AttributeTranslation::class,"attribute_id");
    }
}
