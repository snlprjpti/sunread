<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public $translatedAttributes = ['name'];
    protected $fillable = ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'is_filterable', 'is_visible_on_front', 'is_user_defined','use_in_flat','create_at','updated_at','attribute_group_id'];

    public static $SEARCHABLE = ['slug','name','type'];

    public function attributeOptions()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id');
    }

}
