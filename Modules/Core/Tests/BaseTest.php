<?php

namespace Modules\Core\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BaseTest extends TestCase
{
	use RefreshDatabase;

	protected array $headers;

	public function setUp(): void
	{
		parent::setUp();
		$this->artisan("db:seed");
	}

	public function createAdmin(array $attributes = []): object
	{
		$password = $attributes["password"] ?? "password";
		$role_slug = $attributes["role_slug"] ?? "super-admin";
		$role = Role::where("slug", $role_slug)->firstOrFail();

		$data = [
			"password" => Hash::make($password),
			"role_id" => $role->id
		];
		
		$admin = Admin::factory()->make($data);
		$token = $this->createToken($admin->email, $password);
		$this->headers["Authorization"] = "Bearer {$token}";

		return $admin;
	}

	public function createToekn(string $admin_email, string $password): ?string
	{
		$jwtToken = Auth::guard("admin")
			->setTTL( confit("jwt.admin_jwt_ttl") )
			->attempt([
				"email" => $admin_email,
				"password" => $password
			]);
		return $jwtToken ?? null;
	}
}