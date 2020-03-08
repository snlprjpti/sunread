<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public $translatedAttributes = ['name'];
    protected $fillable = ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'value_per_locale', 'value_per_channel', 'is_filterable', 'is_configurable', 'is_visible_on_front', 'is_user_defined', 'swatch_type', 'use_in_flat'];

    public static function rules($id = 0, $merge = [])
    {
        return array_merge([
            'slug' => ['required', 'unique:attributes,slug' . ($id ? ",$id" : '')],
            'name' => 'required',
            'type' => 'required',
            'attribute_group_id' => 'nullable|exists:attribute_groups,id'
        ], $merge);
    }

    public function attributeOptions()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_option_id');

    }

}
