<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;

class CustomerAddressTableSeeder extends Seeder
{
    public function run(): void
    {
        // DB::table("customer_addresses")->insert(
        //     array_merge(CustomerAddress::factory()->make([
        //         "customer_id" => Customer::latest("id")->first()->id,
        //     ])->toArray(), ["created_at" => now(),
        //     "updated_at" => now()])
        // );
    }
}
