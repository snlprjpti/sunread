<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ImageTypeTableSeeder extends Seeder
{

    public function run()
    {
        DB::table("image_types")->insert([
            [
                "name" => "Main Image",
                "slug" => "main_image",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Small Image",
                "slug" => "small_image",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "thumbnail",
                "slug" => "thumbnail",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Section Background Image",
                "slug" => "section_background_image",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "gallery",
                "slug" => "gallery",
                "created_at" => now(),
                "updated_at" => now()
            ],
        ]);
    }
}
