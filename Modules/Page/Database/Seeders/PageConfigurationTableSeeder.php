<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Page\Entities\PageConfiguration;

class PageConfigurationTableSeeder extends Seeder
{
    public function run(): void
    {
        PageConfiguration::factory()->count(2)->create();
    }
}
