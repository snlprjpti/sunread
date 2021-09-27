<?php

namespace Modules\EmailTemplate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
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
                "email_template_code" => "header",
                "content" => '<!DOCTYPE html> <head> </head> <body> <table class="wrapper" width="100%"><tr><td class="wrapper-inner" align="center"><table class="main" align="center"><tr><td class="header"><a class="logo" href="/"></a></td></tr><tr><td class="main-content">',
                "style" => ""
            ],
            [
                "name" => "Footer",
                "subject" => "Footer",
                "email_template_code" => "footer",
                "content" => '</td></tr><tr><td class="footer"><p class="closing">Thank you!</p></td></tr></table></td></tr></table></body></html>',
                "style" => ""
            ]
        ];

        $data = array_map(function ($template) {
            return [
                "name" => $template["name"],
                "email_template_code" => $template["email_template_code"],
                "subject" => $template["subject"],
                "style" => $template["style"],
                "content" => $template["content"],
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ];
        }, $templates);

        DB::table("email_templates")->insert($data);
    }
}
