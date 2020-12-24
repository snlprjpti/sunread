<?php

namespace Tests\Feature\UserModule;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\User\Entities\Role;
use Tests\AuthTestCase;


class RoleTest extends AuthTestCase
{
    use RefreshDatabase, WithFaker;
    public function setUp():void
    {
        parent::setUp();
    }

    public function test_super_admin_can_fetch_role_list()
    {
        $admin = $this->createAdmin();
        $response = $this->get('/api/admin/roles' ,$this->headers);
        $response->assertOk();
        $response->assertJsonFragment([
            'status' => 'success'
        ]);
        $this->assertTrue($this->checkItemExist('slug','super-admin', $response->json('payload.data')));
    }

    public function test_admin_cannot_fetch_role_list()
    {
        $this->withExceptionHandling();
        $admin_role = factory(Role::class)->create(['slug' => 'admin']);
        $this->createAdmin(['role_id' => $admin_role->id]);

        $response = $this->get('/api/admin/roles' ,$this->headers);
        $response->assertStatus(403);
        $response->assertJsonFragment(['status' => 'error']);
    }

    public function test_super_admin_can_fetch_role1()
    {
        $this->createAdmin();
        $role = Role::where('slug','admin')->first();
        $role_id = $role->id;
        $response = $this->get("/api/admin/roles/$role_id" ,$this->headers);
        $response->assertOk();
        $response->assertJsonFragment([
            'status' => 'success',
            'slug' =>'admin'
        ]);
    }

    public function test_super_admin_can_create_role_with_no_permission()
    {
        $this->createAdmin();
        $role_slug  = $this->faker->slug;
        $role_title = $this->faker->name;

        $role_data = [
            'slug' => $role_slug,
            'title' => $role_title,
            'permissions' => [],
        ];
        $response = $this->post("/api/admin/roles" ,$role_data,$this->headers);
        $response->assertCreated();
        $response->assertJsonFragment($role_data);
    }

    public function test_super_admin_can_create_role_with_permission()
    {
        $this->createAdmin();
        $role_slug  = $this->faker->slug;
        $role_title = $this->faker->name;
        $permissions_array = Permission::factory()->count(2)->create()->pluck('slug')->toArray();
        $role_data = [
            'slug' => $role_slug,
            'title' => $role_title,
            'permissions' => $permissions_array,
        ];
        $response = $this->post("/api/admin/roles" ,$role_data,$this->headers);
        $response->assertCreated();
        $response->assertJsonFragment([
            'slug' => $role_slug,
            'title' => $role_title,
        ]);
        $this->assertTrue($this->checkItemExist('slug',$permissions_array[0],$response->json('payload.permissions')));
        $this->assertTrue($this->checkItemExist('slug',$permissions_array[1],$response->json('payload.permissions')));
    }


    public function test_super_admin_can_update_role()
    {
        $this->createAdmin();
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        $role->permissions()->save($permission);

        //checking the permission exist
        $this->assertTrue($this->checkItemExist('slug',$permission->slug, $role->permissions->toArray()));

        $permissions_array = Permission::factory()->count(2)->create()->pluck('slug')->toArray();
        $new_role_data = [
            'slug' => $this->faker->slug,
            'title' => $this->faker->title,
            'permissions' => $permissions_array,
        ];

        $role_id = $role->id;
        $response = $this->put("/api/admin/roles/$role_id" ,$new_role_data,$this->headers);
        $response->assertJsonFragment([
            "message" => "Role updated successfully."
        ]);
        //checking the old permission doesnt exist
        $this->assertFalse($this->checkItemExist('slug',$permission->slug,$response->json('payload.permissions')));

        //checking the new permission exist
        $this->assertTrue($this->checkItemExist('slug',$permissions_array[0],$response->json('payload.permissions')));
        $this->assertTrue($this->checkItemExist('slug',$permissions_array[1],$response->json('payload.permissions')));

    }

    public function test_super_admin_can_delete_role()
    {
        $this->createAdmin();
        $role = Role::factory()->create();
        $role_id = $role->id;

        $this->assertDatabaseHas('roles', ["slug" => $role->slug]);

        $response = $this->delete("/api/admin/roles/$role_id" ,[],$this->headers);
        $response->assertJsonFragment([
            "status" => 'success',
            "message" => "Role deleted successfully."
        ]);

        $this->assertDatabaseMissing('roles', ["slug" => $role->slug]);
    }

    public function checkItemExist($key, $value,$array)
    {
        return array_search($value, array_column($array, $key)) !== FALSE;
    }

}
