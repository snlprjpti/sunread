<?php

namespace Modules\User\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\User\Entities\Admin;

class AdminInvitationTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Admin::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Admin account";
        $this->route_prefix = "admin";
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasStoreTest = false;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function createAdmin(array $attributes = []): object
    {
        $data = [
            "invitation_token" => \Str::random(20)
        ];

        return Admin::factory()->create($data);
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

    public function testUserShouldBeAbleToAcceptInvitation()
    {
        $post_data = [
            "token" => $this->admin->invitation_token,
            "password" => "new_password",
            "password_confirmation" => "new_password",
        ];
        $response = $this->post(route("{$this->route_prefix}.accept-invitation"), $post_data);

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

}
