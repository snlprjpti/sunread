<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customer_groups')->insert([
            [
                'slug' => 'guest',
                'name' => 'Guest',
                'is_user_defined' => 0
            ]
        ]);
    }
}