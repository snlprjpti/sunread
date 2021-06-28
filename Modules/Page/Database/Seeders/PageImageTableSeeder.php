<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Page\Entities\PageImage;

class PageImageTableSeeder extends Seeder
{
    public function run(): void
    {
        PageImage::factory(2)->create();
    }
}
