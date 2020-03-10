<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\SlugAble;

class AttributeFamily extends Model
{
    use SlugAble;
    protected $fillable = ['name', 'slug'];
    public $timestamps= false;
    public static function rules($id = 0, $merge = [])
    {
        return array_merge([
                'slug' => ['nullable', 'unique:attribute_families,slug' . ($id ? ",$id" : '')],
                'name' => 'required'
            ], $merge);
    }

    public function attributeGroups()
    {
        return $this->hasMany(AttributeGroup::class, 'attribute_family_id');
    }

    public function custom_attributes()
    {
        return (Attribute::class)
            ->join('attribute_groups', 'attributes.attribute_group_id', '=', 'attribute_groups.id')
            ->join('attribute_families', 'attribute_groups.attribute_family_id', '=', 'attribute_families.id')
            ->where('attribute_families.id', $this->id)
            ->select('attributes.*');
    }
}
