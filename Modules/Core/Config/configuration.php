<?php
return[
    "general" => [
        "title" => "General",
        "children" => [
            [
                "title" => "General",
                "subChildren" => [
                    [
                        "title" => "Country Options",
                        "elements" => [
                            [
                                "title" => "Default Country",
                                "path" => "default_country",
                                "type" => "select",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name","iso_2_code"],
                                "default" => "SE",
                                "options" => [],
                                "rules" => "exists:countries,iso_2_code",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Allow Countries",
                                "path" => "allow_countries",
                                "type" => "select",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name", "iso_2_code"],
                                "default" => ["SE"],
                                "options" => [],
                                "rules" => "array",
                                "value_rules" => "exists:countries,iso_2_code",
                                "multiple" => true,
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Zip/Postal Code is Optional for",
                                "path" => "optional_zip_countries",
                                "type" => "select",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name", "iso_2_code"],
                                "default" => [],
                                "options" => [],
                                "rules" => "array",
                                "value_rules" => "exists:countries,iso_2_code",
                                "multiple" => true,
                                "scope" => "website",
                                "is_required" => 1
                            ],
                            [
                                "title" => "State is Optional for",
                                "path" => "general_optional_state",
                                "type" => "select",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name", "iso_2_code"],
                                "default" => [],
                                "options" => [],
                                "rules" => "array",
                                "value_rules" => "exists:countries,iso_2_code",
                                "multiple" => true,
                                "scope" => "website",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "State Options",
                        "elements" => [
                            [
                                "title" => "State Country",
                                "path" => "state_country",
                                "type" => "select",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name", "iso_2_code"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:countries,iso_2_code",
                                "multiple" => false,
                                "scope" => "store",
                                "is_required" => 1
                            ],
                        ]
                    ],
                    [
                        "title" => "Store Information",
                        "elements" => [
                            [
                                "title" => "Store Name",
                                "path" => "store_name",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Store",
                                "pluck" => ["name", "id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:stores,id",
                                "multiple" => false,
                                "scope" => "global",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Store Phone Number",
                                "path" => "store_phone_number",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "channel",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Store Hours of Operation",
                                "path" => "store_hours_operation",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "website",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Country",
                                "path" => "store_country",
                                "type" => "select",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name", "iso_2_code"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:countries,iso_2_code",
                                "multiple" => false,
                                "scope" => "store",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Region/State",
                                "path" => "store_region",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "global",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Zip/Postal Code",
                                "path" => "store_zip_code",
                                "type" => "number",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "numeric",
                                "scope" => "store",
                                "is_required" => 0
                            ],
                            [
                                "title" => "City",
                                "path" => "store_city",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "global",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Street Address",
                                "path" => "store_street_address",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "website",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Street Address Line 2",
                                "path" => "store_address_line2",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "channel",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Store Image",
                                "path" => "store_image",
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "scope" => "store",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Pagination Limit",
                                "path" => "pagination_limit",
                                "type" => "number",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "numeric",
                                "scope" => "global",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Locale Options",
                        "elements" => [
                            [
                                "title" => "Locale",
                                "path" => "store_locale",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Locale",
                                "pluck" => ["name", "id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:locales,id",
                                "multiple" => false,
                                "scope" => "store",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Weight Unit",
                                "path" => "locale_weight_unit",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "kgs",
                                "options" => [
                                    [ "value" => "lbs", "label" => "lbs" ],
                                    [ "value" => "kgs", "label" => "kgs" ],
                                ],
                                "rules" => "in:lbs,kgs",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Global Timezone",
                                "path" => "global_timezone",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\TimeZone",
                                "pluck" => ["name", "id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:time_zones,id",
                                "multiple" => false,
                                "scope" => "website",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Channel Time Zone",
                                "path" => "channel_time_zone",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\TimeZone",
                                "pluck" => ["name", "id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:time_zones,id",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ],

                        ]
                    ]
                ]
            ],
            [
                "title" => "Web",
                "subChildren" => [
                    [
                        "title" => "General",
                        "elements" => [
                            [
                                "title" => "Logo",
                                "path" => "logo",
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png,svg",
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Favicon",
                                "path" => "favicon",
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png,svg",
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Channel Icon",
                                "path" => "channel_icon",
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png,svg",
                                "scope" => "channel",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Default Pages",
                        "elements" => [
                            [
                                "title" => "Home Page",
                                "path" => "home_page",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "channel",
                                "is_required" => 0
                            ],
                            [
                                "title" => "404 Page",
                                "path" => "404_page",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "channel",
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Base URLs",
                        "elements" => [
                            [
                                "title" => "StoreFront Base URL",
                                "path" => "storefront_base_urL",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "website",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Admin Dashboard URL",
                                "path" => "admin_dashboard_url",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "website",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Media URL",
                                "path" => "media_url",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "website",
                                "is_required" => 0
                            ]
                        ]
                    ]
                ]
            ],
            [
                "title" => "Currency Setup",
                "subChildren" => [
                    [
                        "title" => "Currency Options",
                        "elements" => [
                            [
                                "title" => "Channel Currency",
                                "path" => "channel_currency",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code","id"],
                                "default" => "142",
                                "options" => [],
                                "rules" => "exists:currencies,id",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Symbol Position",
                                "path" => "symbol_position",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => 4,
                                "options" => [
                                    [ "value" => 1, "label" => "Before Value" ],
                                    [ "value" => 2, "label" => "Before Value With Space" ],
                                    [ "value" => 3, "label" => "After Value" ],
                                    [ "value" => 4, "label" => "After Value With Space" ],
                                ],
                                "rules" => "in:1,2,3,4",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Minus Sign",
                                "path" => "minus_sign",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "-",
                                "options" => [],
                                "rules" => "",
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Minus Sign Position",
                                "path" => "minus_sign_position",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => 1,
                                "options" => [
                                    [ "value" => 1, "label" => "Before Value" ],
                                    [ "value" => 2, "label" => "Before Symbol" ],
                                    [ "value" => 3, "label" => "After Value" ],
                                    [ "value" => 4, "label" => "After Symbol" ]
                                ],
                                "rules" => "in:1,2,3,4",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Group Seperator",
                                "path" => "group_seperator",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => 2,
                                "options" => [
                                    [ "value" => 1, "label" => "Comma (,)" ],
                                    [ "value" => 2, "label" => "Dot (.)" ],
                                    [ "value" => 3, "label" => "Space()" ],
                                    [ "value" => 4, "label" => "None" ]
                                ],
                                "rules" => "in:1,2,3,4",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Decimal Seperator",
                                "path" => "decimal_seperator",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => 1,
                                "options" => [
                                    [ "value" => 1, "label" => "Comma (,)" ],
                                    [ "value" => 2, "label" => "Dot (.)" ]
                                ],
                                "rules" => "in:1,2",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ]
                        ]
                    ],
                ]
            ],
            [
                "title" => "Website Defaults",
                "subChildren" => [
                    [
                        "title" => "Channel",
                        "elements" => [
                            [
                                "title" => "Default Channel",
                                "path" => "website_default_channel",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Channel",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:channels,id",
                                "multiple" => false,
                                "scope" => "website",
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Store",
                        "elements" => [
                            [
                                "title" => "Default Store",
                                "path" => "website_default_store",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Store",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:stores,id",
                                "multiple" => false,
                                "scope" => "website",
                                "is_required" => 0
                            ]
                        ]
                    ],
                ]
            ],
            [
                "title" => "Email",
                "subChildren" => [
                    [
                        "title" => "General",
                        "elements" => [
                            [
                                "title" => "Sender Name",
                                "path" => "email_sender_name",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "store",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Sender Email",
                                "path" => "email_sender_email",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "store",
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Templates",
                        "elements" => [
                            [
                                "title" => "Header",
                                "path" => "header",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "store",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Footer",
                                "path" => "footer",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "store",
                                "is_required" => 0
                            ]
                        ]
                    ],
                ]
            ]

        ]
    ],
    "catalog" => [
        "title" => "Catalog",
        "children" => [
            [
                "title" => "Catalog",
                "subChildren" => [
                    [
                        "title" => "Product Fields Auto-Generation",
                        "elements" => [
                            [
                                "title" => "Mask for SKU",
                                "path" => "catalog_masks_sku",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "store",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Mask for Meta Title",
                                "path" => "catalog_meta_title",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "channel",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Mask for Meta Keywords",
                                "path" => "catalog_meta_keywords",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "store",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Mask for Meta description",
                                "path" => "catalog_meta_description",
                                "type" => "textarea",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "scope" => "website",
                                "is_required" => 0
                            ],
                        ]

                    ]
                ]
            ]
        ]
    ],
    "customer" => [
        "title" => "Customer",
        "children" => [
            [
                "title" => "Customer",
                "subChildren" => [
                    [
                        "title" => "New Account Options",
                        "elements" => [
                            [
                                "title" => "Default Customer Group",
                                "path" => "customer_default_customer_group",
                                "type" => "select",
                                "provider" => "Modules\Customer\Entities\CustomerGroup",
                                "pluck" => ["name", "id"],
                                "default" => "1",
                                "options" => [],
                                "rules" => "exists:customer_groups,id",
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Require Email Confirmation",
                                "path" => "require_email_confirmation",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => 2,
                                "options" => [
                                    [ "value" => 1, "label" => "Yes" ],
                                    [ "value" => 2, "label" => "No" ],
                                ],
                                "rules" => "",
                                "multiple" => false,
                                "scope" => "website",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Password Options",
                        "elements" => [
                            [
                                "title" => "Recovery Link Expiration Period (hours)",
                                "path" => "recovery_link_expiration_period",
                                "type" => "number",
                                "provider" => "",
                                "pluck" => [],
                                "default" => 2,
                                "options" => [],
                                "rules" => "",
                                "multiple" => false,
                                "scope" => "global",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Minimum Password Length",
                                "path" => "minimum_password_length",
                                "type" => "number",
                                "provider" => "",
                                "pluck" => [],
                                "default" => 8,
                                "options" => [],
                                "rules" => "",
                                "multiple" => false,
                                "scope" => "global",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Email Templates",
                        "elements" => [
                            [
                                "title" => "Default Welcome Email Template",
                                "path" => "default_welcome_email",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "multiple" => false,
                                "scope" => "store",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Confirmation Link Email Template",
                                "path" => "confirmation_link_email",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "multiple" => false,
                                "scope" => "store",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Welcome Email Template",
                                "path" => "welcome_email",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "array",
                                "value_rules" => "",
                                "multiple" => true,
                                "scope" => "website",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Forgot Password Email Template",
                                "path" => "forgot_password",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "multiple" => false,
                                "scope" => "store",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Reset Password Email Template",
                                "path" => "reset_password",
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "",
                                "multiple" => false,
                                "scope" => "store",
                                "is_required" => 1
                            ]
                        ]
                    ]
                ]
            ]

        ]
    ]
];
?>
