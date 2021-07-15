<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Customer\Entities\Customer;

class CustomerAddressTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("customer_addresses")->insert(
            [
                "customer_id" => Customer::inRandomOrder()->first()->id,
                "first_name" => "John",
                "middle_name" => "Nic",
                "last_name" => "Doe",
                "address1" => "Tmerico",
                "country_id" => null,
                "region_id" => null,
                "city_id" => null,
                "postcode" => "12345",
                "phone" => "987654321",
                "default_billing_address" => 1,
                "default_shipping_address" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ]
        );
    }
}
