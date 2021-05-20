<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(\Modules\Core\Database\Seeders\CoreDatabaseSeeder::class);
        $this->call(\Modules\User\Database\Seeders\UserDatabaseSeeder::class);
        $this->call(\Modules\Customer\Database\Seeders\CustomerDatabaseSeeder::class);
        $this->call(\Modules\Category\Database\Seeders\CategoryDatabaseSeeder::class);
        $this->call(\Modules\Attribute\Database\Seeders\AttributeDatabaseSeeder::class);
        $this->call(\Modules\Brand\Database\Seeders\BrandDatabaseSeeder::class);
        $this->call(\Modules\Product\Database\Seeders\ProductDatabaseSeeder::class);
        $this->call(\Modules\Review\Database\Seeders\ReviewDatabaseSeeder::class);
        $this->call(\Modules\UrlRewrite\Database\Seeders\UrlRewriteDatabaseSeeder::class);
    }
}
