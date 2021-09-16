<?php

return [
    "general" => [
        [
            "event" => "Forgot Password",
            "label" => "Forgot Password",
            "email_template_code" => "forgot_password"
        ],
        [
            "event" => "Password Reset Successful",
            "label" => "Reset Password",
            "email_template_code" => "reset_password"
        ],
        [
            "event" => "Contact",
            "label" => "Contact Form",
            "email_template_code" => "contact_form"
        ]
    ],
    "customer" => [
        [
            "event" => "new account",
            "label" => "New Account",
            "email_template_code" => "new_account"
        ],
        [
            "event" => "New Account Confirmed",
            "label" => "Welcome Email",
            "email_template_code" => "welcome_email"
        ],
        [
            "event" => "New Order",
            "label" => "New Order",
            "email_template_code" => "new_order"
        ],
        [
            "event" => "Order Update",
            "label" => "Order Update",
            "email_template_code" => "order_update"
        ]
    ],
    "guest" => [
        [
            "event" => "New Guest Order",
            "label" => "New Guest Order",
            "email_template_code" => "new_guest_order"
        ],

        [
            "event" => "Order Update (Guest)",
            "label" => "Guest Order Update",
            "email_template_code" => "order_update_guest"
        ]
    ]
];
