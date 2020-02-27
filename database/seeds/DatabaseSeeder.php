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
        //$this->call(\Modules\User\Database\Seeders\UserDatabaseSeeder::class);
        $this->call(\Modules\Customer\Database\Seeders\CustomerDatabaseSeeder::class);
    }
}
