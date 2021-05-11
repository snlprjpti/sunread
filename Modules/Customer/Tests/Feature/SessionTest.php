<?php

namespace Modules\Customer\Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerGroup;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionTest extends TestCase
{
    use RefreshDatabase;

    protected object $customer, $fake_customer;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        $this->artisan("db:seed", ["--force" => true]);

        $this->customer = $this->createCustomer();
        $this->fake_customer = Customer::factory()->make();
    }

    public function createCustomer(array $attributes = []): object
    {
        $password = $attributes["password"] ?? "password";

        $data = [
            "password" => Hash::make($password),
            "customer_group_id" => CustomerGroup::first()->id
        ];

        return Customer::factory()->create($data);
    }

    /**
     * Tests
    */

    public function testCustomerCanLogin()
    {
        $post_data = [
            "email" => $this->customer->email,
            "password" => "password"
        ];
        $response = $this->post(route("customer.session.login"), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.users.users.login-success")
        ]);
    }

    public function testInvalidCredentialsShouldNotBeAbleToLogin()
    {
        $post_data = [
            "email" => $this->customer->email,
            "password" => "wrong_password"
        ];
        $response = $this->post(route("customer.session.login"), $post_data);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.users.users.login-error")
        ]);
    }

    public function testInvalidUserShouldNotBeAbleToLogin()
    {
        $post_data = [
            "email" => $this->fake_customer->email,
            "password" => null
        ];
        $response = $this->post(route("customer.session.login"), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testCustomerCanRequestResetLink()
    {
        $post_data = ["email" => $this->customer->email];
        $response = $this->post(route("customer.forget-password.store"), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => "Reset Link sent to your email {$this->customer->email}"
        ]);
    }

    public function testCustomerCanResetPassword()
    {
        $reset_token = Password::broker('customers')->createToken($this->customer);
        $post_data = [
            "email" => $this->customer->email,
            "password" => "new_password",
            "password_confirmation" => "new_password",
            "token" => $reset_token
        ];
        $response = $this->post(route("customer.reset-password.store"), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.users.reset-password.password-reset-success")
        ]);
    }

    public function testInvalidCustomerShouldNotBeAbleToRequestResetLink()
    {
        $post_data = ["email" => $this->fake_customer->email];
        $response = $this->post(route("customer.forget-password.store"), $post_data);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testCustomerShouldNotBeAbleToResetPasswordWithInvalidToken()
    {
        $reset_token = \Str::random(16);
        $post_data = [
            "email" => $this->customer->email,
            "password" => "new_password",
            "password_confirmation" => "new_password",
            "token" => $reset_token
        ];
        $response = $this->post(route("customer.reset-password.store"), $post_data);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.users.token.token-generation-problem")
        ]);
    }

    public function testCustomerCanLogout()
    {
        $post_data = [
            "email" => $this->customer->email,
            "password" => "password"
        ];
        $response = $this->post(route("customer.session.login"), $post_data);
        $jwt_token = $response->json()["payload"]["token"];
        $this->headers["Authorization"] = "Bearer {$jwt_token}";

        /**
         * This logout should be successful because token is valid
         */
        $response = $this->withHeaders($this->headers)->get(route("customer.session.logout"));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.users.users.logout-success")
        ]);

        /**
         * This logout should be unsuccessful because token is invalidated
         */
        $response = $this->withHeaders($this->headers)->get(route("customer.session.logout"));
        $response->assertStatus(401);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }
}
