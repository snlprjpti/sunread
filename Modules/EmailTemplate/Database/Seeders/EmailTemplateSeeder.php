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
                "name" => "Header",
                "subject" => "Header",
                "email_template_code" => "header",
                "content" => headerTemplate(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Footer",
                "subject" => "Footer",
                "email_template_code" => "footer",
                "content" => footerTemplate(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Welcome",
                "subject" => "Welcome",
                "email_template_code" => "welcome_email",
                "content" => welcomeTemplate(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "New Account",
                "subject" => "New Account",
                "email_template_code" => "new_account",
                "content" => newAccountTemplate(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Confirmation Email",
                "subject" => "Confirmation Email",
                "email_template_code" => "confirm_email",
                "content" => confirmTemplate(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Forgot Password",
                "subject" => "Forgot Password",
                "email_template_code" => "forgot_password",
                "content" => forgotPasswordTemplate(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Reset Password",
                "subject" => "Reset Password",
                "email_template_code" => "reset_password",
                "content" => resetPasswordTemplate(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Contact Form",
                "subject" => "Contact Form",
                "email_template_code" => "contact_form",
                "content" => contactForm(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Order Confirmation",
                "subject" => "Order Confirmation",
                "email_template_code" => "new_order",
                "content" => orderConfirmTemplate(),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
        ];

        DB::table("email_templates")->insert($templates);
    }
}
