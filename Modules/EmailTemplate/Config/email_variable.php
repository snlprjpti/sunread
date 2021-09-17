<?php

return [
    "General" => [
        [
            "variable" => "store_url",
            "label" => "Store URL",
            "type" => "string",
            "source" => [ "Website" , "Channel", "Store" ],
            "availability" => ["all"]
        ],
        [
            "variable" => "store_name",
            "label" => "Store Name",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_phone_number",
            "label" => "Store Phone Number",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_country",
            "label" => "Store Country",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_state",
            "label" => "Store Region / State",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_post_code",
            "label" => "Store Postal / Zip Code",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_city",
            "label" => "Store City",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_address_line_1",
            "label" => "Store Address Line 1",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_address_line_2",
            "label" => "Store Address Line 2",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_vat_number",
            "label" => "Store VAT number",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
        [
            "variable" => "store_email_address",
            "label" => "Store Email Address",
            "type" => "string",
            "source" => "configuration",
            "availability" => ["all"],
        ],
        [
            "variable" => "store_email_logo_url",
            "label" => "Store Email Logo URL",
            "type" => "string",
            "source" => "configuration",
            "availability" => [ "all" ],
        ],
    ],

    "Customer" => [
        [
            "variable" => "customer_id",
            "label" => "Customer ID",
            "type" => "",
            "source" => "",
            "availability" => [ "new_account", "welcome_email", "forgot_password", "reset_password", "new_order", "order_update" ],
        ],
        [
            "variable" => "customer_name",
            "label" => "Customer Name",
            "type" => "",
            "source" => "",
            "availability" => [ "new_account", "welcome_email", "forgot_password", "reset_password", "new_order", "order_update", "order_update_guest" ],
        ],
        [
            "variable" => "customer_email_address",
            "label" => "Customer Email Address",
            "type" => "",
            "source" => "",
            "availability" => [ "new_account", "welcome_email", "forgot_password", "reset_password", "new_order", "new_guest_order", "order_update", "order_update_guest" ],
        ],
        [
            "variable" => "customer_dashboard_url",
            "label" => "Customer Dashboard URL",
            "type" => "",
            "source" => "",
            "availability" => [ "new_account", "welcome_email", "forgot_password", "reset_password", "new_order", "order_update" ],
        ],
        [
            "variable" => "account_confirmation_url",
            "label" => "Account Confirmation URL",
            "type" => "",
            "source" => "",
            "availability" => [ "new_account" ],
        ],
    ],

    "Password Reset" => [
        [
            "variable" => "password_reset_url",
            "label" => "Password Reset URL",
            "type" => "",
            "source" => "",
            "availability" => [ "forgot_password" ],
        ],
    ],

    "Order" => [
        [
            "variable" => "order_id",
            "label" => "Order ID",
            "type" => "",
            "source" => "",
            "availability" => [ "new_order", "new_guest_order", "order_update", "order_update_guest" ],
        ],
        [
            "variable" => "order_items",
            "label" => "Order Items (Table)",
            "type" => "",
            "source" => "",
            "availability" => [ "new_order", "new_guest_order", "order_update", "order_update_guest" ],
        ],
        [
            "variable" => "billing_address",
            "label" => "Billing Address",
            "type" => "",
            "source" => "",
            "availability" => [ "new_order", "new_guest_order", "order_update", "order_update_guest" ],
        ],
        [
            "variable" => "shipping_address",
            "label" => "Shipping Address",
            "type" => "",
            "source" => "",
            "availability" => [ "new_order", "new_guest_order", "order_update", "order_update_guest" ],
        ],
        [
            "variable" => "order",
            "label" => "Order",
            "type" => "",
            "source" => "",
            "availability" => [ "new_order", "new_guest_order", "order_update", "order_update_guest" ],
        ],
    ]
];
