<?php

namespace Modules\Country\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountryTableSeeder extends Seeder
{
    public function run(): void
    {
        $default_countries = [
            [
                "name" => "Nepal",
            ]
        ];


        $data = array_map(function($country) {
            return [
                "alpha_2_code" => Str::random(10),
                "alpha_3_code" => Str::random(10),
                "numeric_code" => rand(1,5),
                "iso_2_code" => Str::random(10),
                "iso_3_code" => Str::random(10),
                "dialing_code" => Str::random(10),
                "name" => $country["name"] ?? "Nepal",
                "created_at" => now(),
                "updated_at" => now()
            ];
        }, $default_countries);

        DB::table("countries")->insert($data);
    }
}
