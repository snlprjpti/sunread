<?php


namespace Modules\Customer\Repositories;


use Modules\Core\Eloquent\Repository;
use Modules\Customer\Entities\CustomerGroup;

class CustomerAddressRepository extends Repository
{

    public function model()
    {
        return CustomerAddress::class;
    }
}
