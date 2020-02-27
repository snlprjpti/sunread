<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('customer_groups')->delete();

        DB::table('customer_groups')->insert([
            [
                'id' => 1,
                'slug' => 'guest',
                'name' => 'Guest',
                'is_user_defined' => 0,
            ]
            //insert more groups here
        ]);
    }
}