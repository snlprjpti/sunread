<?php

namespace Modules\User\Tests\Feature;

use Modules\User\Entities\Admin;
use Modules\Core\Tests\BaseTestCase;

class UserTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Admin::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Admin";
        $this->route_prefix = "admin.users";
        $this->hasStatusTest = true;
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
