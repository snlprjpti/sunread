<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('locales')->delete();

        DB::table('locales')->insert([
            [
                'id' => 1,
                'code' => 'en',
                'name' => 'English',
            ], [
                'id' => 2,
                'code' => 'fr',
                'name' => 'French',
            ]
        ]);
    }
}
