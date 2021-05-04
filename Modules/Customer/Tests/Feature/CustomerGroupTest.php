<?php

namespace Modules\Customer\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Customer\Entities\CustomerGroup;

class CustomerGroupTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = CustomerGroup::class;
        $this->model_name = "Customer Group";
        $this->route_prefix = "admin.groups";
        $this->default_resource_id = CustomerGroup::latest()->first()->id;
        $this->fake_resource_id = 0;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "is_user_defined" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null
        ]);
    }
}
