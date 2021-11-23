<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CacheTableSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                "name" => "Configuration",
                "slug" => "configuration",
                "description" => "All Configuration",
                "key" => "configuration-data",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Core Cache",
                "slug" => "core-cache",
                "description" => "All Core Data",
                "key" => "sf",
                "created_at" => now(),
                "updated_at" => now()
            ],
        ];

        DB::table("cache")->insert($templates);
    }
}
