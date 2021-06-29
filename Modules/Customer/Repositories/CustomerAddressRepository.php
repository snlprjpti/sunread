<?php

namespace Modules\Customer\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Customer\Entities\CustomerAddress;

class CustomerAddressRepository extends BaseRepository
{
    public function __construct(CustomerAddress $customer_address)
    {
        $this->model = $customer_address;
        $this->model_key = "customers.addresses";

        $this->rules = [
            "address1" => "required|min:2|max:500",
            "address2" => "sometimes",
            "country" => "required",
            "state" => "required",
            "city" => "required",
            "postcode" => "required",
            "phone" => "required",
            "default_address" => "sometimes|boolean"
        ];
    }
}
