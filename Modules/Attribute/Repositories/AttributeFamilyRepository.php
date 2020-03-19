<?php


namespace Modules\Attribute\Repositories;

use Modules\Attribute\Contracts\AttributeFamilyInterface;
use Modules\Attribute\Entities\AttributeFamily;
use Modules\Core\Eloquent\Repository;
use Modules\Core\Traits\Sluggable;

class AttributeFamilyRepository extends Repository implements AttributeFamilyInterface
{
    use Sluggable;
    /**
     * @inheritDoc
     */
    public function model()
    {
        return AttributeFamily::class;
    }

    public static function rules($id = 0, $merge = [])
    {
        return array_merge([
            'slug' => ['nullable', 'unique:attribute_families,slug' . ($id ? ",$id" : '')],
            'name' => 'required'
        ], $merge);
    }

}
