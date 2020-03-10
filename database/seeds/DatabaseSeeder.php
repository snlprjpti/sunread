<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(\Modules\Core\Database\Seeders\CoreDatabaseSeeder::class);
        //$this->call(\Modules\User\Database\Seeders\UserDatabaseSeeder::class);
        //$this->call(\Modules\Customer\Database\Seeders\CustomerDatabaseSeeder::class);
        //$this->call(\Modules\Category\Database\Seeders\CategoryDatabaseSeeder::class);
        $this->call(\Modules\Attribute\Database\Seeders\AttributeDatabaseSeeder::class);
    }
}
