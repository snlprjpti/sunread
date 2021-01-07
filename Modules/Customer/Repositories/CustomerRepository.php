<?php


namespace Modules\Customer\Repositories;


use Modules\Core\Eloquent\Repository;
use Modules\Customer\Entities\Customer;

class CustomerRepository extends Repository
{

    public function model()
    {
        return Customer::class;
    }
}
