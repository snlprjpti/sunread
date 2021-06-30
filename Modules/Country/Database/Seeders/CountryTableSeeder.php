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
        $data = [
            [
                "alpha_2_code" => "AF",
                "alpha_3_code" => "AFG",
                "numeric_code" => "004",
                "iso_2_code" => "AF",
                "iso_3_code" => "AFG",
                "dialing_code" => "+93",
                "name" => "Afghanistan"
            ],
            [
                "alpha_2_code" => "NP",
                "alpha_3_code" => "NPL",
                "numeric_code" => "524",
                "iso_2_code" => "NP",
                "iso_3_code" => "NPL",
                "dialing_code" => "+977",
                "name" => "Nepal"
            ],
            [
                "alpha_2_code" => "IN",
                "alpha_3_code" => "IND",
                "numeric_code" => "356",
                "iso_2_code" => "IN",
                "iso_3_code" => "IND",
                "dialing_code" => "+91",
                "name" => "India"
            ]
        ];

        DB::table("countries")->insert($data);
    }
}
