<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('currencies')->delete();

        DB::table('currencies')->insert([
            [
                'id' => 1,
                'code' => 'USD',
                'erp_code' => 'ENU',
                'name' => 'US Dollar',
                'symbol' => '$',
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'id' => 2,
                'code' => 'EUR',
                'erp_code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
