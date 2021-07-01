<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Entities\Page;

class PageTableSeeder extends Seeder
{
    public function run(): void
    {
        Page::factory()->count(2)->create();
    }
}