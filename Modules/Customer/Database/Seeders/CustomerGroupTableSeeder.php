<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Tax\Entities\CustomerTaxGroup;

class CustomerGroupTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("customer_groups")->insert([
            "slug" => "general",
            "name" => "General",
            "customer_tax_group_id" => CustomerTaxGroup::factory()->create()->id,
            "is_user_defined" => 0,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}