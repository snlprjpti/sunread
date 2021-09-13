<?php

namespace Modules\EmailTemplate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmailVariableTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("email_variables")->insert([
            [
                "name" => "Base URL",
                "value" => "local"
            ],
            [
                "name" => "Store Email",
                "value" => "store@gmail.com"
            ],
        ]);
    }
}
