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
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/header.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Footer",
                "subject" => "Footer",
                "email_template_code" => "footer",
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/footer.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Welcome",
                "subject" => "Welcome",
                "email_template_code" => "welcome_email",
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/welcomeEmail.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "New Account",
                "subject" => "New Account",
                "email_template_code" => "new_account",
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/newAccount.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Confirmation Email",
                "subject" => "Confirmation Email",
                "email_template_code" => "confirm_email",
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/confirmEmail.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Forgot Password",
                "subject" => "Forgot Password",
                "email_template_code" => "forgot_password",
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/forgotPassword.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Reset Password",
                "subject" => "Reset Password",
                "email_template_code" => "reset_password",
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/resetPassword.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Contact Form",
                "subject" => "Contact Form",
                "email_template_code" => "contact_form",
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/contactForm.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "New Order",
                "subject" => "New Order",
                "email_template_code" => "new_order",
                "content" => file_get_contents(module_path('EmailTemplate', 'Resources/views/templates/newOrder.blade.php')),
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
        ];

        DB::table("email_templates")->insert($templates);
    }
}
