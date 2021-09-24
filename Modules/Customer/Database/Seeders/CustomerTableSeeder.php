<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Customer\Entities\Customer;
use Modules\EmailTemplate\Entities\EmailTemplate;

class CustomerTableSeeder extends Seeder
{
    public function run(): void
    {
        EmailTemplate::factory()->create();
        Customer::factory()->create();
    }
}
