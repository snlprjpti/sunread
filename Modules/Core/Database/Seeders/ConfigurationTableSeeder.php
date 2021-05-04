<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configurations')->insert([
            [
                "scope" => "default",
                "scope_id" => 0,
                "path" => "web/seo/use_rewrites",
                "value" => "1",
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
