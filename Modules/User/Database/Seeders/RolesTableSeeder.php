<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->delete();

        DB::table('roles')->delete();

        DB::table('roles')->insert([
                'id' => 1,
                'name' => 'Administrator',
                'slug' => 'super-admin',
                'description' => 'Administrator role',
                'permission_type' => 'all'
            ]);
    }
}