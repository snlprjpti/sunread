<?php

namespace Modules\Cart\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Cart\Entities\Cart;
use Illuminate\Database\Eloquent\Model;

class CartDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Cart::create(["customer_id" => 1]);

        // $this->call("OthersTableSeeder");
    }
}
