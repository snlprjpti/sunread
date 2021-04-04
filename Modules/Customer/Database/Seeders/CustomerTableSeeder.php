<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('customers')->insert([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'customer@example.net',
            'password' => bcrypt("password")
        ]);
    }
}
