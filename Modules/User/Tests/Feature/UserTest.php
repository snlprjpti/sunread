<?php

namespace Modules\User\Tests\Feature;

use Modules\User\Entities\Admin;
use Modules\Core\Tests\BaseTestCase;

class UserTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = Admin::class;
        $this->model_name = "Admin";
        $this->route_prefix = "admin.users";
        $this->default_resource_id = $this->model::latest()->first()->id;
        $this->fake_resource_id = 0;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function getCreateData(): array
    {
        return array_merge($this->model::factory()->make()->toArray(), [
            "password" => "password",
            "password_confirmation" => "password"
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "email" => null
        ]);
    }
}
