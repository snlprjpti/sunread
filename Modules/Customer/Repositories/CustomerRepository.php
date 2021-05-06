<?php

namespace Modules\Customer\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Customer\Entities\Customer;

class CustomerRepository extends BaseRepository
{
    protected $model, $model_key;

    public function __construct(Customer $customer)
    {
        $this->model = $customer;
        $this->model_key = "customers.customers";

        $this->rules = [
            "first_name" => "required|min:2|max:200",
            "last_name" => "required|min:2|max:200",
            "email" => "required|email|unique:customers,email",
            "gender" => "sometimes|in:male,female",
            "date_of_birth" => "date|before:today",
            "status" => "sometimes|boolean",
            "customer_group_id" => "nullable|exists:customer_groups,id",
            "subscribed_to_news_letter" => "sometimes|boolean",
            "password" => "sometimes|min:6|confirmed"
        ];
    }


}