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
        return Admin::factory()->create(["invitation_token" => \Str::random(20) ]);
    }

    public function testUserShouldBeAbleToAcceptInvitation()
    {
        $post_data = [
            "invitation_token" => $this->createAdmin()->invitation_token,
            "password" => "new_password",
            "password_confirmation" => "new_password",
        ];
        $response = $this->post($this->getRoute("accept-invitation", $post_data));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }
    public function testUserShouldNotBeAbleToAcceptInvitationWithInvalidToken()
    {
        $invitation_token = \Str::random(20);
        $post_data = [
            "invitation_token" => $invitation_token,
            "password" => "new_password",
            "password_confirmation" => "new_password",
        ];
        $response = $this->post($this->getRoute("accept-invitation", $post_data));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.response.not-found",  ["name" => $this->model_name])
        ]);
    }

}
