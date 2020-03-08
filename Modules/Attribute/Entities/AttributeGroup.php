<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\SlugAble;

class AttributeGroup extends Model
{
    use SlugAble;
    protected $fillable = ['name', 'position', 'is_user_defined' ,'slug','attribute_family_id'];
    public $timestamps = false;
    public static function  rules($id = 0 , $merge = [])
    {
        return
            array_merge([
                'slug' => ['nullable', 'unique:attribute_groups,slug' . ($id ? ",$id" : '')],
                'name' => 'required',
                'attribute_family_id' => 'required|exists:attribute_families,id'
            ], $merge);
        
    }
}
