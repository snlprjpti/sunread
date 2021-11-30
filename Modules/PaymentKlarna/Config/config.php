<?php

return [
    "title" => "Klarna Payments",
    "slug" => "klarna",
    "repository" => "Modules\PaymentMethods\Repositories\KlarnaRepository",
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
            "multiple" => false,
            "scope" => "channel",
            "is_required" => 1,
            "sort_by" => ""
        ],
        [
            "title" => "Enabled",
            "path" => "payment_methods_klarna_enabled",
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
        "slug" => "klarna_api_config",
        "repository" => "Modules\PaymentMethods\Repositories\KlarnaRepository",
        "elements" => [
            [
                "title" => "Endpoint",
                "path" => "payment_methods_klarna_api_config_endpoint",
                "type" => "select",
                "provider" => "",
                "pluck" => [],
                "default" => "klarna_north_america",
                "options" => [
                    [ "value" => "klarna_north_america", "label" => "Klarna Payments (North America)" ],
                    [ "value" => "klarna_europe", "label" => "Klarna Payments (Europe)" ],
                    [ "value" => "klarna_oceania", "label" => "Klarna Payments (Oceania)" ]
                ],
                "rules" => "in:klarna_north_america,klarna_europe,klarna_oceania",
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
                "default" => "",
                "options" => [],
                "rules" => "",
                "multiple" => false,
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
                "default" => "",
                "options" => [],
                "rules" => "",
                "multiple" => false,
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
        ]
    ],
    "klarna_design" => [
        "title" => "Klarna Design",
        "slug" => "klarna_design",
        "repository" => "Modules\PaymentMethods\Repositories\KlarnaRepository",
        "elements" => [
            [
                "title" => "Detail Color",
                "path" => "payment_methods_klarna_design_color",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "",
                "options" => [],
                "rules" => "",
                "multiple" => false,
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
                "default" => "",
                "options" => [],
                "rules" => "",
                "multiple" => false,
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
                "multiple" => false,
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
                "default" => "",
                "options" => [],
                "rules" => "",
                "multiple" => false,
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
                "multiple" => false,
                "scope" => "channel",
                "is_required" => 0,
                "sort_by" => ""
            ]
        ]
    ]
];
