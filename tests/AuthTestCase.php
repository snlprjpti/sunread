<?php


namespace Tests;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\Admin;
use Modules\User\Entities\Role;


class AuthTestCase extends TestCase
{

    protected  $headers;
    public function setUp():void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function createAdmin($attributes = [])
    {
        $password = isset($attributes['password'])? $attributes['password']: 'password';
        $admin_slug = isset($attributes['slug'])? $attributes['slug']: 'super-admin';
        $role = Role::where('slug', $admin_slug)->first();
        $attributes = [
            'password' => Hash::make($password),
            'role_id' => $role->id
        ];
        $admin = factory(Admin::class)->create($attributes);
        $token =  $this->createToken($admin->email, $password);
        $this->headers['Authorization'] = 'Bearer '.$token;
        return $admin;
    }

    private function createToken($email, $password)
    {
        $jwtToken = null;
        $admin_jwt_ttl = config('jwt.admin_jwt_ttl');
        if (!$jwtToken = Auth::guard('admin')->setTTl($admin_jwt_ttl)->attempt(['email' => $email, 'password' => $password])) {
             return false;
        }
        return $jwtToken;

    }

}
