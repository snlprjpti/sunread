<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CacheManagementTableSeederTableSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                "name" => "Configuration",
                "slug" => "configuration",
                "description" => "All Configuration",
                "tag" => "config",
                "key" => "configuration-data",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Core Cache",
                "slug" => "core-cache",
                "description" => "All Core Data",
                "tag" => "core",
                "key" => "sf",
                "created_at" => now(),
                "updated_at" => now()
            ],
        ];

        DB::table("cache_management")->insert($templates);
    }
}
