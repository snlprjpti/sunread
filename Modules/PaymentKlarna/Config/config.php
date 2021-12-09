<?php

return [
    "title" => "Klarna Payments",
    "slug" => "klarna",
    "repository" => "Modules\PaymentKlarna\Repositories\KlarnaRepository",
    "elements" => [
        [
            "title" => "Title",
            "path" => "payment_methods_klarna_title",
            "type" => "text",
            "provider" => "",
            "pluck" => [],
            "default" => "Klarna Payment",
            "options" => [],
            "rules" => "",
            "scope" => "channel",
            "is_required" => 1,
            "sort_by" => ""
        ],
        [
            "title" => "Enabled",
            "path" => "payment_methods_klarna",
            "type" => "select",
            "provider" => "",
            "pluck" => [],
            "default" => 1,
            "options" => [
                [ "value" => 1, "label" => "Yes" ],
                [ "value" => 0, "label" => "No" ]
            ],
            "rules" => "boolean",
            "multiple" => false,
            "scope" => "channel",
            "is_required" => 0,
            "sort_by" => ""
        ],
        [
            "title" => "New Order Status",
            "path" => "payment_methods_klarna_new_order_status",
            "type" => "select",
            "provider" => "Modules\Sales\Entities\PendingOrderStatus",
            "pluck" => ["slug", "name"],
            "default" => "pending",
            "options" => [],
            "rules" => "exists:order_statuses,slug",
            "multiple" => false,
            "scope" => "channel",
            "is_required" => 1,
            "sort_by" => ""
        ],
        [
            "title" => "Allowed Countries",
            "path" => "payment_methods_klarna_allowed_countries",
            "type" => "select",
            "provider" => "",
            "pluck" => [],
            "default" => "all_allowed_countries",
            "options" => [
                [ "value" => "all_allowed_countries", "label" => "All Allowed Countries" ],
                [ "value" => "specific_countries", "label" => "Specific Counrtry" ]
            ],
            "rules" => "in:all_allowed_countries,specific_countries",
            "multiple" => false,
            "scope" => "channel",
            "is_required" => 0,
            "sort_by" => ""
        ],
        [
            "title" => "Payment From Specific Countries",
            "path" => "payment_methods_klarna_specific_countries",
            "type" => "select",
            "provider" => "Modules\Country\Entities\Country",
            "pluck" => ["name", "iso_2_code"],
            "default" => "",
            "options" => [],
            "rules" => "exists:countries,iso_2_code",
            "multiple" => false,
            "scope" => "channel",
            "is_required" => 0,
            "sort_by" => ""
        ]
    ],
    "api_configuration" => [
        "title" => "Klarna API Configuration",
        "slug" => "klarna",
        "repository" => "Modules\PaymentKlarna\Repositories\KlarnaRepository",
        "elements" => [
            [
                "title" => "Endpoint",
                "path" => "payment_methods_klarna_api_config_endpoint",
                "type" => "select",
                "provider" => "",
                "pluck" => [],
                "default" => "europe",
                "options" => [
                    [ "value" => "north_america", "label" => "Klarna Payments (North America)" ],
                    [ "value" => "europe", "label" => "Klarna Payments (Europe)" ],
                    [ "value" => "oceania", "label" => "Klarna Payments (Oceania)" ]
                ],
                "rules" => "in:north_america,europe,oceania",
                "multiple" => false,
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Klarna API Username",
                "path" => "payment_methods_klarna_api_config_username",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "PK21291_f93bbbc9e7cf",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Klarna API Password",
                "path" => "payment_methods_klarna_api_config_password",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "jygyOyXm7rhVTPZO",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Mode",
                "path" => "payment_methods_klarna_api_config_mode",
                "type" => "select",
                "provider" => "",
                "pluck" => [],
                "default" => "playground",
                "options" => [
                    [ "value" => "playground", "label" => "Playground" ],
                    [ "value" => "production", "label" => "Production" ]
                ],
                "rules" => "in:playground,production",
                "multiple" => false,
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Merchant Url Terms Page",
                "path" => "payment_methods_klarna_api_config_terms_page",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "https://www.example.com/terms.html",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Merchant Url Checkout Page",
                "path" => "payment_methods_klarna_api_config_checkout_page",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "https://www.example.com/checkout.html",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Merchant Url Confirmation Page",
                "path" => "payment_methods_klarna_api_config_confirmation_page",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "https://www.example.com/confirmation.html",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Merchant Url Push Notification",
                "path" => "payment_methods_klarna_api_config_push_notify",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "https://www.example.com/api/push",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
        ]
    ],
    "klarna_design" => [
        "title" => "Klarna Design",
        "slug" => "klarna",
        "repository" => "Modules\PaymentKlarna\Repositories\KlarnaRepository",
        "elements" => [
            [
                "title" => "Detail Color",
                "path" => "payment_methods_klarna_design_color",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "#FF9900",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            [
                "title" => "Border Color",
                "path" => "payment_methods_klarna_design_border_color",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "blue",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            [
                "title" => "Selected Border Color",
                "path" => "payment_methods_klarna_design_selected_border_color",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            [
                "title" => "Text Color",
                "path" => "payment_methods_klarna_design_text_color",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "#FF9900",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            [
                "title" => "Border Radius",
                "path" => "payment_methods_klarna_design_border_radius",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            [
                "title" => "Color CheckBox",
                "path" => "payment_methods_klarna_design_color_checkbox",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "#FF9900",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            [
                "title" => "Color CheckBox CheckMark",
                "path" => "payment_methods_klarna_design_color_checkbox_checkmark",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "#FF9900",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            [
                "title" => "Color Header",
                "path" => "payment_methods_klarna_design_color_header",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "#FF9900",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            [
                "title" => "Color Link",
                "path" => "payment_methods_klarna_design_color_link",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "#FF9900",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ],
            
        ]
    ]
];
