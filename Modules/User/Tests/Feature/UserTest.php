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

    public function testAdminCanResendInvitationToUser()
    {
        $newAdmin = Admin::factory()->create(["invitation_token" => \Str::random(20) ]);
        $response = $this->withHeaders($this->headers)->put($this->getRoute("resend-invitation", [$newAdmin->id]));

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminShouldNotBeAbleToResendInvitationIfUserNoLongerInvitedStatus()
    {
        $newAdmin = Admin::factory()->create(["invitation_token" => null]);

        $response = $this->withHeaders($this->headers)->put($this->getRoute("resend-invitation", [$newAdmin->id]));

        $response->assertStatus(403);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.users.users.already-active", ["name" => $this->model_name])
        ]);
    }
}
