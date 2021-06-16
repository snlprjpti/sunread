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
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "options" => [],
                                "rules" => 'required',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "multiple" => false,
                                "scope" => "global"
                            ],
                            [
                                "title" => "Allow Countries",
                                "path" => "allow_countries",
                                "type" => "checkbox",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code", "id"],
                                "default" => [],
                                "options" => [],
                                "rules" => "required",
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "scope" => "website"
                            ],
                            [
                                "title" => "Zip/Postal Code is Optional for",
                                "path" => "optional_zip_countries",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code", "id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "nullable",
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "multiple" => false,
                                "scope" => "channel"
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
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code", "id"],
                                "default" => "",
                                "options" => [],
                                "rules" => 'required',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "multiple" => false,
                                "scope" => "store"
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
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "multiple" => false,
                                "scope" => "global"
                            ],
                            [
                                "title" => "Store Phone Number",
                                "path" => "store_phone_number",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "channel"
                            ],
                            [
                                "title" => "Store Hours of Operation",
                                "path" => "store_hours_operation",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "sometimes|nullable",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "website"
                            ],
                            [
                                "title" => "Country",
                                "path" => "store_country",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code", "id"],
                                "default" => [],
                                "options" => [],
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "multiple" => true,
                                "scope" => "store"
                            ],
                            [
                                "title" => "Region/State",
                                "path" => "store_region",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "global"
                            ],
                            [
                                "title" => "Zip/Postal Code",
                                "path" => "store_zip_code",
                                "type" => "number",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "required|numeric",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "store"
                            ],
                            [
                                "title" => "City",
                                "path" => "store_city",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "global"
                            ],
                            [
                                "title" => "Street Address",
                                "path" => "store_street_address",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "website"
                            ],
                            [
                                "title" => "Street Address Line 2",
                                "path" => "store_address_line2",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => "sometimes|nullable",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "channel"
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
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "store"
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
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "scope" => "website"
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
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "multiple" => false,
                                "scope" => "global"
                            ],
                            [
                                "title" => "Default Display Currency",
                                "path" => "default_display_currency",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "options" => [],
                                "rules" => "required",
                                "showIn" => ["channel","website","default","store"],
                                "multiple" => false,
                                "scope" => "website"
                            ],
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
                                "rules" => 'nullable',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "scope" => "store"
                            ],
                            [
                                "title" => "Mask for Meta Title",
                                "path" => "catalog_meta_title",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => 'nullable',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "scope" => "channel"
                            ],
                            [
                                "title" => "Mask for Meta Keywords",
                                "path" => "catalog_meta_keywords",
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => 'nullable',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "scope" => "store"
                            ],
                            [
                                "title" => "Mask for Meta description",
                                "path" => "catalog_meta_description",
                                "type" => "textarea",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "rules" => 'nullable',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "scope" => "website"
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
                                "rules" => 'required',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "scope" => "global"
                            ],
                            [
                                "title" => "Default Customer Group",
                                "path" => "customer_default_customer_group",
                                "type" => "select",
                                "provider" => "Modules\Customer\Entities\CustomerGroup",
                                "pluck" => ["name", "id"],
                                "default" => "1",
                                "options" => [],
                                "rules" => 'required',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                                "multiple" => false,
                                "scope" => "website"
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
?>