<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageScope;

class PageTableSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::create([
            "title" => "Test",
            "slug" => "test",
            "meta_title" => "Test",
            "meta_description" => "Test",
            "meta_keywords" => "Test",
            "status" => 1,
            "position" => 1 
        ]);

        PageScope::create([
            "page_id" => $page->id,
            "scope" => "website",
            "scope_id" => 1
        ]);
    }
}
