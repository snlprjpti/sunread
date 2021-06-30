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
                "iso_2_code" => "AF",
                "iso_3_code" => "AFG",
                "numeric_code" => "004",
                "dialing_code" => "+93",
                "name" => "Afghanistan"
            ],
            [
                "iso_2_code" => "NP",
                "iso_3_code" => "NPL",
                "numeric_code" => "524",
                "dialing_code" => "+977",
                "name" => "Nepal"
            ],
            [
                "iso_2_code" => "IN",
                "iso_3_code" => "IND",
                "numeric_code" => "356",
                "dialing_code" => "+91",
                "name" => "India"
            ]
        ];

        $data = array_map(function($country) {
            return [
                "iso_2_code" => $country["iso_2_code"],
                "iso_3_code" => $country["iso_3_code"],
                "numeric_code" => $country["numeric_code"],
                "dialing_code" => $country["dialing_code"],
                "name" => $country["name"],
                "created_at" => now(),
                "updated_at" => now()
            ];
        }, $default_countries);

        DB::table("countries")->insert($data);
    }
}
