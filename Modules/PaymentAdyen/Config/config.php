<?php

return [
    "title" => "Adyen Payments",
    "slug" => "adyen",
    "repository" => "Modules\PaymentAdyen\Repositories\AdyenRepository",
    "elements" => [
        [
            "title" => "Title",
            "path" => "payment_methods_adyen_title",
            "type" => "text",
            "provider" => "",
            "pluck" => [],
            "default" => "Adyen Payment",
            "options" => [],
            "rules" => "",
            "scope" => "channel",
            "is_required" => 1,
            "sort_by" => ""
        ],
        [
            "title" => "Enabled",
            "path" => "payment_methods_adyen",
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
            "path" => "payment_methods_adyen_new_order_status",
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
            "path" => "payment_methods_adyen_allowed_countries",
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
            "path" => "payment_methods_adyen_specific_countries",
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
        "title" => "Adyen API Configuration",
        "slug" => "adyen",
        "repository" => "Modules\PaymentAdyen\Repositories\AdyenRepository",
        "elements" => [
            [
                "title" => "Adyan Merchant Account",
                "path" => "payment_methods_adyen_api_config_merchant_account",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "SailRacingInternationalABECOM",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Adyen API Key",
                "path" => "payment_methods_adyen_api_config_api_key",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "AQE7hmfxKo3IYh1Gw0m/n3Q5qf3Ve4pEAaFLW2xYwVGlimNZkMZiGclvBztALDHZ19uTMGgAYB1+gnsFKCMQwV1bDb7kfNy1WIxIIkxgBw==-aenSfjq/a7wcG+mlC+pVz5Wzb9SprAzKZChMcwaF2KI=-ea7J]J?y#X3Sg*xT",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Adyen Client Key",
                "path" => "payment_methods_adyen_api_config_client_key",
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "test_YYIEG3NUB5E4VPGXZXFA7WMNMI6IU6VV",
                "options" => [],
                "rules" => "",
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ],
            [
                "title" => "Mode",
                "path" => "payment_methods_adyen_api_config_mode",
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
                "title" => "Enviroment",
                "path" => "payment_methods_adyen_environment",
                "type" => "select",
                "provider" => "",
                "pluck" => [],
                "default" => "test",
                "options" => [
                    [ "value" => "test", "label" => "Test" ],
                    [ "value" => "live", "label" => "Europe Live" ],
                    [ "value" => "live-au", "label" => "Australia Live" ],
                    [ "value" => "live-us", "label" => "US Live" ]
                ],
                "rules" => "in:test,live,live-au,live-us",
                "multiple" => false,
                "scope" => "channel",
                "is_required" => 1,
                "sort_by" => ""
            ]
        ]
    ]
];
