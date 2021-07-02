<?php

namespace Modules\Tax\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Tax\Entities\TaxRateTaxRule;

class TaxRateTaxRuleTableSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();
        TaxRateTaxRule::factory()->create();
    }
}
