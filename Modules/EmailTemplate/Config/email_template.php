<?php

return [
    "general" => [
        [
            "event" => "Forgot Password",
            "intended_user" => "all",
            "label" => "Forgot Password",
            "email_template_code" => "forgot_password"
        ],
        [
            "event" => "Password Reset Successful",
            "intended_user" => "all",
            "label" => "Reset Password",
            "email_template_code" => "reset_password"
        ],
        [
            "event" => "Contact",
            "intended_user" => "",
            "label" => "Contact Form",
            "email_template_code" => "contact_form"
        ]
    ],
    "customer" => [
        [
            "event" => "new account",
            "intended_user" => "customer",
            "label" => "New Account",
            "email_template_code" => "new_account"
        ],
        [
            "event" => "New Account Confirmed",
            "intended_user" => "customer",
            "label" => "Welcome Email",
            "email_template_code" => "welcome_email"
        ],
        [
            "event" => "New Order",
            "intended_user" => "customer",
            "label" => "New Order",
            "email_template_code" => "new_order"
        ],
        [
            "event" => "Order Update",
            "intended_user" => "customer",
            "label" => "Order Update",
            "email_template_code" => "order_update"
        ]
    ],
    "guest" => [
        [
            "event" => "New Guest Order",
            "intended_user" => "guest",
            "label" => "New Guest Order",
            "email_template_code" => "new_guest_order"
        ],

        [
            "event" => "Order Update (Guest)",
            "intended_user" => "guest",
            "label" => "Guest Order Update",
            "email_template_code" => "order_update_guest"
        ]
    ]
];
