<?php

namespace Modules\User\Tests\Feature;

use Modules\User\Entities\Role;
use Modules\Core\Tests\BaseTestCase;

class RoleTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = Role::class;
        $this->model_name = "Role";
        $this->route_prefix = "admin.roles";
        $this->default_resource_id = Role::latest()->first()->id;
        $this->fake_resource_id = 0;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "description" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null
        ]);
    }
}
