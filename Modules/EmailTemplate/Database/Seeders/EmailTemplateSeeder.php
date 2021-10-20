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
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Footer",
                "subject" => "Footer",
                "email_template_code" => "footer",
                "content" => '</td></tr><tr><td class="footer"><p class="closing">Thank you!</p></td></tr></table></td></tr></table></body></html>',
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Welcome",
                "subject" => "Welcome",
                "email_template_code" => "welcome_email",
                "content" => "<h2>Welcome Here</h2>",
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Forgot Password",
                "subject" => "Forgot Password",
                "email_template_code" => "forgot_password",
                "content" => "<h2>Forgot Password</h2>",
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Reset Password",
                "subject" => "Reset Password",
                "email_template_code" => "Reset_password",
                "content" => "<h2>Reset Password</h2>",
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
        ];

        DB::table("email_templates")->insert($templates);
    }
}
