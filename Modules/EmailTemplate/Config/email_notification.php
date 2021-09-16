<?php

return [
    "group" => [
        [
            "event" => "new account",
            "intended_user" => "customer",
            "email_template" => "New Account",
            "email_template_code" => "new_account"
        ],
        [
            "event" => "New Account Confirmed",
            "intended_user" => "customer",
            "email_template" => "Welcome Email",
            "email_template_code" => "welcome_email"
        ],
        [
            "event" => "Forgot Password",
            "intended_user" => "all",
            "email_template" => "Forgot Password",
            "email_template_code" => "forgot_password"
        ],
        [
            "event" => "Password Reset Successful",
            "intended_user" => "all",
            "email_template" => "Reset Password",
            "email_template_code" => "reset_password"
        ],
        [
            "event" => "New Order",
            "intended_user" => "customer",
            "email_template" => "New Order",
            "email_template_code" => "new_order"
        ],
        [
            "event" => "New Guest Order",
            "intended_user" => "guest",
            "email_template" => "New Guest Order",
            "email_template_code" => "new_guest_order"
        ],
        [
            "event" => "Order Update",
            "intended_user" => "customer",
            "email_template" => "Order Update",
            "email_template_code" => "order_update"
        ],
        [
            "event" => "Order Update (Guest)",
            "intended_user" => "guest",
            "email_template" => "Guest Order Update",
            "email_template_code" => "order_update_guest"
        ],
        [
            "event" => "Contact",
            "intended_user" => "",
            "email_template" => "Contact Form",
            "email_template_code" => "contact_form"
        ],
    ]
];
