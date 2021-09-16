<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(CurrencyTableSeeder::class);
        $this->call(ExchangeRateTableSeeder::class);
        $this->call(WebsiteTableSeeder::class);
        $this->call(ChannelTableSeeder::class);
        $this->call(StoreTableSeeder::class);
        $this->call(ConfigurationTableSeeder::class);
        $this->call(LocalesTableSeeder::class);
    }
}
