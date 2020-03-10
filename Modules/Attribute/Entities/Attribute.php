<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public $translatedAttributes = ['name'];
    protected $fillable = ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'is_filterable', 'is_visible_on_front', 'is_user_defined','use_in_flat','create_at','updated_at'];

    public static function rules($id = 0, $merge = [])
    {
        return array_merge([
            'slug' => ['nullable', 'unique:attributes,slug' . ($id ? ",$id" : '')],
            'name' => 'required',
            'type' => 'required',
            'attribute_group_id' => 'nullable|exists:attribute_groups,id'
        ], $merge);
    }

    public function attributeOptions()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_option_id');

    }

    public function createUpdateTranslation(Array $translation_attributes)
    {
        foreach ($translation_attributes as $translation_attribute){
            $check_attributes = ['locale' => $translation_attribute['locale'], 'attribute_id' => $this->id];
            $attribute_translation = AttributeTranslation::firstorNew($check_attributes);
            $attribute_translation->fill($translation_attribute);
            $attribute_translation->save();
        }

    }





}
