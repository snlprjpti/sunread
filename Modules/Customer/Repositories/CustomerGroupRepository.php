<?php

namespace Modules\Customer\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Customer\Entities\CustomerGroup;

class CustomerGroupRepository extends BaseRepository
{
    public function __construct(CustomerGroup $customer_group)
    {
        $this->model = $customer_group;
        $this->model_key = "customers.groups";
        $this->rules = [
            "name" => "required|min:2|max:100",
            "slug" => "nullable|unique:customer_groups,slug"
        ];
    }
}
