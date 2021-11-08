<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SalesDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(OrderTableSeeder::class);
        $this->call(OrderItemTableSeeder::class);
        $this->call(OrderTaxTableSeeder::class);
        $this->call(OrderTaxItemTableSeeder::class);
    }
}
