<?php

namespace Modules\User\Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\Role;
use Modules\User\Entities\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AccountTest extends TestCase
{
	use RefreshDatabase;

    protected object $admin;
	protected array $headers;
    public $model, $model_name;

	public function setUp(): void
	{
		parent::setUp();
		Schema::disableForeignKeyConstraints();
		$this->artisan("db:seed", ["--force" => true]);

        $this->admin = $this->createAdmin();
        $this->model = Admin::class;

        $this->fake_admin = $this->model::factory()->make();
        $this->model_name = "Admin account";
	}

    /**
     * Generate Admin data
     */
	public function createAdmin(array $attributes = []): object
	{
		$password = $attributes["password"] ?? "password";
		$role_slug = $attributes["role_slug"] ?? "super-admin";
		$role = Role::where("slug", $role_slug)->firstOrFail();

		$data = [
			"password" => Hash::make($password),
			"role_id" => $role->id
		];
		
		$admin = Admin::factory()->create($data);
		$token = $this->createToken($admin->email, $password);
		$this->headers["Authorization"] = "Bearer {$token}";

		return $admin;
	}

	public function createToken(string $admin_email, string $password): ?string
	{
		$jwtToken = Auth::guard("admin")
			->setTTL( config("jwt.admin_jwt_ttl") )
			->attempt([
				"email" => $admin_email,
				"password" => $password
			]);
		return $jwtToken ?? null;
	}

    /**
     * Tests
     */

    public function testAdminCanFetchAccountDetails()
    {
        $response = $this->withHeaders($this->headers)->get(route("admin.account.show"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.fetch-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminShouldNotBeAbleToFetchAccountDetailsWithoutAuth()
    {
        $response = $this->withHeaders(["Authorization" => "Bearer invalid_token"])->get(route("admin.account.show"));

        $response->assertStatus(401);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => "Unauthenticated."
        ]);
    }

    public function testAdminCanUpdateAccountDetails()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "_method" => "PUT",
            "current_password" => "password",
            "password" => "new_password",
            "password_confirmation" => "new_password"
        ]);

        $response = $this->withHeaders($this->headers)->post(route("admin.account.update"), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminShouldNotBeAbleToUpdateAccountDetailsWithInvalidPassword()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "_method" => "PUT",
            "current_password" => "invalid_password",
            "password" => "new_password",
            "password_confirmation" => "new_password"
        ]);

        $response = $this->withHeaders($this->headers)->post(route("admin.account.update"), $post_data);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            "status" => "error",
            "message" => __("core::app.users.users.incorrect-password")
        ]);
    }

    public function testAdminCanUpdateProfileImage()
    {
        Storage::fake();
        $post_data = [
            "image" => UploadedFile::fake()->image("image.png")
        ];

        $response = $this->withHeaders($this->headers)->post(route("admin.account.image.update"), $post_data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => "Profile image updated successfully."
        ]);
    }

    public function testAdminShouldNotBeAbleToUpdateProfileImageWithInvalidImage()
    {
        $post_data = [
            "image" => null
        ];

        $response = $this->withHeaders($this->headers)->post(route("admin.account.image.update"), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testAdminShouldBeAbleToDeleteProfileImage()
    {
        $response = $this->withHeaders($this->headers)->delete(route("admin.account.image.delete"));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => "Profile image deleted successfully."
        ]);
    }
}
