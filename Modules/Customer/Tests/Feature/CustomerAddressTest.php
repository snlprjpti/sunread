<?php

namespace Modules\Customer\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;

class CustomerAddressTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = CustomerAddress::class;

        parent::setUp();

        $this->admin = $this->createAdmin();
        $this->model_name = "Customer Address";
        $this->route_prefix = "admin.customers.addresses";
        $this->append_to_route = Customer::latest("id")->first()->id;
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "address2" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "address1" => null
        ]);
    }
}
