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
                                "pluck" => ["name","id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:countries,id",
                                "showIn" => ["channel", "website", "default", "store"],
                                "multiple" => false,
                                "scope" => "global",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Allow Countries",
                                "path" => "allow_countries",
                                "type" => "checkbox",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name", "id"],
                                "default" => [],
                                "options" => [],
                                "rules" => "array",
                                "value_rules" => "exists:countries,id",
                                "showIn" => ["channel", "website", "default", "store"],
                                "scope" => "website",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Zip/Postal Code is Optional for",
                                "path" => "optional_zip_countries",
                                "type" => "select",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name", "id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:countries,id",
                                "showIn" => ["channel", "website", "default", "store"],
                                "multiple" => false,
                                "scope" => "channel",
                                "is_required" => 0
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
                                "pluck" => ["name", "id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:countries,id",
                                "showIn" => ["channel", "website", "default", "store"],
                                "multiple" => false,
                                "scope" => "store",
                                "is_required" => 0
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
                                "showIn" => ["channel","website","default","store"],
                                "multiple" => false,
                                "scope" => "global",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Store Phone Number",
                                "path" => "store_phone_number",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "nullable",
                                "showIn" => ["channel","website","default","store"],
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
                                "rules" => "nullable",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "website",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Country",
                                "path" => "store_country",
                                "type" => "select",
                                "provider" => "Modules\Country\Entities\Country",
                                "pluck" => ["name", "id"],
                                "default" => [],
                                "options" => [],
                                "rules" => "array",
                                "value_rules" => "exists:countries,id",
                                "showIn" => ["channel","website","default","store"],
                                "multiple" => true,
                                "scope" => "store",
                                "is_required" => 0
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
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "global",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Zip/Postal Code",
                                "path" => "store_zip_code",
                                "type" => "number",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "nullable|numeric",
                                "showIn" => ["channel","website","default","store"],
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
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "global",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Street Address",
                                "path" => "store_street_address",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "nullable",
                                "showIn" => ["channel","website","default","store"],
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
                                "rules" => "nullable",
                                "showIn" => ["channel","website","default","store"],
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
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "store",
                                "is_required" => 0
                            ],
                        ]
                    ]
                ]
            ],
            [
                "title" => "Web",
                "subChildren" => [
                    [
                        "title" => "Search Engine Optimization",
                        "elements" => [
                            [
                                "title" => "Use Web Server Rewrites",
                                "path" => "use_rewrite",
                                "type" => "radio",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "1",
                                "options" => [
                                    [ "value" => 1, "label" => "Yes" ],
                                    [ "value" => 0, "label" => "No" ]
                                ],
                                "rules" => "nullable|in:0,1",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "store",
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Base URLs",
                        "elements" => [
                            [
                                "title" => "Base URL",
                                "path" => "base_url",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "nullable",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "website",
                                "is_required" => 0
                            ],

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
                                "title" => "Base Currency",
                                "path" => "base_currency",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:currencies,id",
                                "showIn" => ["channel","website","default","store"],
                                "multiple" => false,
                                "scope" => "global",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Default Display Currency",
                                "path" => "default_display_currency",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "exists:currencies,id",
                                "showIn" => ["channel","website","default","store"],
                                "multiple" => false,
                                "scope" => "website",
                                "is_required" => 1
                            ],
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
                                "showIn" => ["website"],
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
                                "showIn" => ["website"],
                                "multiple" => false,
                                "scope" => "website",
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
                                "rules" => "nullable",
                                "showIn" => ["channel", "website", "default", "store"],
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
                                "rules" => "nullable",
                                "showIn" => ["channel", "website", "default", "store"],
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
                                "rules" => "nullable",
                                "showIn" => ["channel", "website", "default", "store"],
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
                                "rules" => "nullable",
                                "showIn" => ["channel", "website", "default", "store"],
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
                "title" => "Customer Configuration",
                "subChildren" => [
                    [
                        "title" => "Create New Account Options",
                        "elements" => [
                            [
                                "title" => "Enable Automatic Assignment to Customer Group",
                                "path" => "customer_auto_customer_group",
                                "type" => "radio",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "1",
                                "options" => [
                                    [ "value" => 1, "label" => "Yes" ],
                                    [ "value" => 0, "label" => "No" ]
                                ],
                                "rules" => "in:0,1",
                                "showIn" => ["channel", "website", "default", "store"],
                                "scope" => "global",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Default Customer Group",
                                "path" => "customer_default_customer_group",
                                "type" => "select",
                                "provider" => "Modules\Customer\Entities\CustomerGroup",
                                "pluck" => ["name", "id"],
                                "default" => "1",
                                "options" => [],
                                "rules" => "exists:customer_groups,id",
                                "showIn" => ["channel", "website", "default", "store"],
                                "multiple" => false,
                                "scope" => "website",
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
