<?php


namespace Modules\Customer\Repositories;


use Modules\Core\Eloquent\Repository;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerGroup;

class CustomerGroupRepository extends Repository
{

    public function model()
    {
        return CustomerGroup::class;
    }
}
