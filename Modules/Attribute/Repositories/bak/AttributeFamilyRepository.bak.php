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

}
