<?php

namespace Modules\EmailTemplate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                "template_name" => "Header",
                "template_subject" => "Header",
                "template_content" => '<!DOCTYPE html> <head> </head> <body> <table class="wrapper" width="100%"><tr><td class="wrapper-inner" align="center"><table class="main" align="center"><tr><td class="header"><a class="logo" href="/"></a></td></tr><tr><td class="main-content">',
                "is_default" => 1
            ],
            [
                "template_name" => "Footer",
                "template_subject" => "Footer",
                "template_content" => '</td></tr><tr><td class="footer"><p class="closing">Thank you!</p></td></tr></table></td></tr></table></body></html>',
                "is_default" => 1
            ],
            [
                "template_name" => "Forgot-Admin-Password",
                "template_subject" => "Password Reset Confirmation",
                "template_content" => '[{"id":1},{"content":"texts"},{"id":2}]',
                "is_default" => 1
            ]
        ];

        $data = array_map(function ($template) {
            return [
                "template_name" => $template["template_name"],
                "template_subject" => $template["template_subject"],
                "template_content" => $template["template_content"],
                "is_default" => $template["is_default"],
                "created_at" => now(),
                "updated_at" => now()
            ];
        }, $templates);

        DB::table("email_templates")->insert($data);
    }
}
