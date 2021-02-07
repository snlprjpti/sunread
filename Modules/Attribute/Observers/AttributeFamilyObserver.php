<?php


namespace Modules\Attribute\Observers;

use Modules\Core\Facades\Audit;
use Modules\Attribute\Entities\AttributeFamily;

class AttributeFamilyObserver
{
    public function created(AttributeFamily $attribute_family)
    {
        Audit::log($attribute_family, __FUNCTION__);
    }

    public function updated(AttributeFamily $attribute_family)
    {
        Audit::log($attribute_family, __FUNCTION__);
    }

    public function deleted(AttributeFamily $attribute_family)
    {
        Audit::log($attribute_family, __FUNCTION__);
    }
}
