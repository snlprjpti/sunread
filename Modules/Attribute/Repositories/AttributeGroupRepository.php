<?php


namespace Modules\Attribute\Repositories;


use Modules\Attribute\Contracts\AttributeGroupInterface;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Core\Eloquent\Repository;
use Modules\Core\Traits\Sluggable;

class AttributeGroupRepository extends Repository implements  AttributeGroupInterface
{
    use Sluggable;
    /**
     * @inherit from parent repository
     */
    public function model()
    {
        return AttributeGroup::class;
    }

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
