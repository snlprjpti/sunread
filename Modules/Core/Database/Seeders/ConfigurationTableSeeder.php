<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configurations')->insert([
            [
                "scope" => "global",
                "scope_id" => 0,
                "path" => "default_country",
                "value" => json_encode("SE"),
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "scope" => "store",
                "scope_id" => 1,
                "path" => "header",
                "value" => "1",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "scope" => "store",
                "scope_id" => 1,
                "path" => "footer",
                "value" => "2",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "scope" => "store",
                "scope_id" => 1,
                "path" => "welcome_email",
                "value" => "3",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "scope" => "store",
                "scope_id" => 1,
                "path" => "new_account",
                "value" => "4",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "scope" => "store",
                "scope_id" => 1,
                "path" => "confirm_email",
                "value" => "5",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "scope" => "store",
                "scope_id" => 1,
                "path" => "forgot_password",
                "value" => "6",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "scope" => "store",
                "scope_id" => 1,
                "path" => "reset_password",
                "value" => "7",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "scope" => "store",
                "scope_id" => 1,
                "path" => "contact_form",
                "value" => "8",
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
