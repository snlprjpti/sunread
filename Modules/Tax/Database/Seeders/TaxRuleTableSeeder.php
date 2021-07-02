<?php

namespace Modules\Tax\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Tax\Entities\TaxRule;

class TaxRuleTableSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        TaxRule::factory()->create();
    }
}
