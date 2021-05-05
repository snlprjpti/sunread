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
        $this->model = CustomerGroup::class;
        parent::setUp();
        $this->admin = $this->createAdmin();
        $this->model_name = "Customer Group";
        $this->route_prefix = "admin.groups";
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
