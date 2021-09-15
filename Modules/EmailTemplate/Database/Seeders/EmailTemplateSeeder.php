<?php

namespace Modules\EmailTemplate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                "name" => "Header",
                "subject" => "Header",
                "content" => '<!DOCTYPE html> <head> </head> <body> <table class="wrapper" width="100%"><tr><td class="wrapper-inner" align="center"><table class="main" align="center"><tr><td class="header"><a class="logo" href="/"></a></td></tr><tr><td class="main-content">',
                "is_default" => 1
            ],
            [
                "name" => "Footer",
                "subject" => "Footer",
                "content" => '</td></tr><tr><td class="footer"><p class="closing">Thank you!</p></td></tr></table></td></tr></table></body></html>',
                "is_default" => 1
            ],
            [
                "name" => "Forgot-Admin-Password",
                "subject" => "Password Reset Confirmation",
                "content" => '[{"id":1},{"content":"texts"},{"id":2}]',
                "is_default" => 1
            ]
        ];

        $data = array_map(function ($template) {
            return [
                "name" => $template["name"],
                "slug" => Str::slug($template["name"]),
                "subject" => $template["subject"],
                "content" => $template["content"],
                "is_default" => $template["is_default"],
                "created_at" => now(),
                "updated_at" => now()
            ];
        }, $templates);

        DB::table("email_templates")->insert($data);
    }
}
