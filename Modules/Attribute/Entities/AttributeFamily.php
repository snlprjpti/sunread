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
}
