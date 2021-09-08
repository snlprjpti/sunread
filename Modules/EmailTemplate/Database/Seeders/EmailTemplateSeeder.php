<?php

namespace Modules\EmailTemplate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("email_templates")->insert([
            [
                "template_name" => "Header",
                "template_subject" => "Header",
                "template_content" => "",
                "is_default" => 1
            ],
            [
                "template_name" => "Footer",
                "template_subject" => "Footer",
                "template_content" => '</td></tr><tr><td class="footer"><p class="closing">{{trans "Thank you, %store_name" store_name=$store.frontend_name}}!</p></td></tr></table></td></tr></table></body>',
                "is_default" => 1
            ],
            [
                "template_name" => "Forgot-Admin-Password",
                "template_subject" => "Password Reset Confirmation",
                "template_content" => "",
                "is_default" => 1
            ],
            [
                "template_name" => "Forgot-Customer-Password",
                "template_subject" => "Password Reset Confirmation",
                "template_content" => "",
                "is_default" => 1
            ],
            [
                "template_name" => "Forgot-Customer-Password",
                "template_subject" => "Password Reset Confirmation",
                "template_content" => "",
                "is_default" => 1
            ],
        ]);
    }
}
