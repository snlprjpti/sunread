<?php

return [
    "name" => "sales",
    'configuration_merge' => true,
    'configuration' => [
        "sales" => [
            "title" => "Sales",
            "position" => 3,
            "children" => [
                [
                    "title" => "Sales",
                    "slug" => "sales",
                    "subChildren" => [
                        [
                            "title" => "Tax Classes",
                            "slug" => "tax_classes",
                            "elements" => [
                                [
                                    "title" => "Default Tax Class for Product",
                                    "path" => "default_tax_class_for_product",
                                    "type" => "select",
                                    "provider" => "Modules\Tax\Entities\ProductTaxGroup",
                                    "pluck" => ["name", "id"],
                                    "default" => "1",
                                    "options" => [],
                                    "rules" => "exists:product_tax_groups,id",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 1,
                                    "sort_by" => "name"
                                ],
                                [
                                    "title" => "Default Tax Class for Customer",
                                    "path" => "default_tax_class_for_customer",
                                    "type" => "select",
                                    "provider" => "Modules\Tax\Entities\CustomerTaxGroup",
                                    "pluck" => ["name", "id"],
                                    "default" => "1",
                                    "options" => [],
                                    "rules" => "exists:customer_tax_groups,id",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 1,
                                    "sort_by" => "name"
                                ],
                            ]
                        ],
                        [
                            "title" => "Calculation Settings",
                            "slug" => "calculation_settings",
                            "elements" => [
                                [
                                    "title" => "Tax Calculation Method Based On",
                                    "path" => "tax_calculation_method_based_on",
                                    "type" => "select",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => 1,
                                    "options" => [
                                        [ "value" => 1, "label" => "Total" ],
                                        [ "value" => 2, "label" => "Raw Total" ],
                                        [ "value" => 3, "label" => "Unit Price" ],
                                    ],
                                    "rules" => "in:1,2,3",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 1,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Tax Calculation Based On",
                                    "path" => "tax_calculation_based_on",
                                    "type" => "select",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => 1,
                                    "options" => [
                                        [ "value" => 1, "label" => "Shipping Address" ],
                                        [ "value" => 2, "label" => "Billing Address" ],
                                    ],
                                    "rules" => "in:1,2",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 1,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Catalog Prices",
                                    "path" => "tax_catalog_prices",
                                    "type" => "select",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => 1,
                                    "options" => [
                                        [ "value" => 1, "label" => "Excluding Tax" ],
                                        [ "value" => 2, "label" => "Including Tax" ],
                                    ],
                                    "rules" => "in:1,2",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 1,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Shipping Prices",
                                    "path" => "tax_shipping_prices",
                                    "type" => "select",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => 1,
                                    "options" => [
                                        [ "value" => 1, "label" => "Excluding Tax" ],
                                        [ "value" => 2, "label" => "Including Tax" ],
                                    ],
                                    "rules" => "in:1,2",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 1,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Apply Customer Tax",
                                    "path" => "apply_customer_tax",
                                    "type" => "select",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => 1,
                                    "options" => [
                                        [ "value" => 1, "label" => "After Discount" ],
                                        [ "value" => 2, "label" => "Before Discount" ],
                                    ],
                                    "rules" => "in:1,2",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 1,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Apply Discount on Prices",
                                    "path" => "apply_discount_on_prices",
                                    "type" => "select",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => 1,
                                    "options" => [
                                        [ "value" => 1, "label" => "Excluding Tax" ],
                                        [ "value" => 2, "label" => "Including Tax" ],
                                    ],
                                    "rules" => "in:1,2",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 1,
                                    "sort_by" => ""
                                ],
                            ]
                        ]
                    ]
                ],
                [
                    "title" => "Shipping Settings",
                    "slug" => "shipping_settings",
                    "subChildren" => [
                        [
                            "title" => "Origins",
                            "slug" => "origins",
                            "elements" => [
                                [
                                    "title" => "Country",
                                    "path" => "shipping_settings_origins_country",
                                    "type" => "select",
                                    "provider" => "Modules\Country\Entities\Country",
                                    "pluck" => ["iso_2_code", "name"],
                                    "default" => "",
                                    "options" => [],
                                    "rules" => "exists:countries,iso_2_code",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Region/State",
                                    "path" => "shipping_settings_origins_region_state",
                                    "type" => "select",
                                    "provider" => "Modules\Country\Entities\Region",
                                    "pluck" => ["id", "name"],
                                    "default" => "",
                                    "options" => [],
                                    "rules" => "exists:regions,id",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "ZIP/Postal Code",
                                    "path" => "shipping_settings_origins_zip_postal_code",
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
                                // [
                                //     "title" => "City",
                                //     "path" => "shipping_settings_origins_city",
                                //     "type" => "text",
                                //     "provider" => "Modules\Country\Entities\City",
                                //     "pluck" => ["id", "name"],
                                //     "default" => "",
                                //     "options" => [],
                                //     "rules" => "exists:cities,id",
                                //     "multiple" => false,
                                //     "scope" => "channel",
                                //     "is_required" => 0,
                                //     "sort_by" => ""
                                // ],
                                [
                                    "title" => "Street Address",
                                    "path" => "shipping_settings_origins_street_address",
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
                                    "title" => "Street Address Line 2",
                                    "path" => "shipping_settings_origins_street_address_line_2",
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
                    ]
                ],
                [
                    "title" => "Payment Methods",
                    "slug" => "payment_methods",
                    "subChildren" => [
                        [
                            "title" => "Bank Transfer Payment",
                            "slug" => "bank_transfer",
                            "elements" => [
                                [
                                    "title" => "Enabled",
                                    "path" => "payment_methods_bank_transfer",
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
                                    "title" => "Title",
                                    "path" => "payment_methods_bank_transfer_title",
                                    "type" => "text",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => "Bank Transfer Payment",
                                    "options" => [],
                                    "rules" => "",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "New Order Status",
                                    "path" => "payment_methods_bank_transfer_new_order_status",
                                    "type" => "select",
                                    "provider" => "Modules\Sales\Entities\OrderStatus",
                                    "pluck" => ["slug", "name"],
                                    "default" => "pending",
                                    "options" => [],
                                    "rules" => "exists:order_statuses,slug",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Payment From Applicable Countries",
                                    "path" => "payment_methods_bank_transfer_payment_from_applicable_countries",
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
                                    "path" => "payment_methods_bank_transfer_payment_from_specific_countries",
                                    "type" => "select",
                                    "provider" => "Modules\Country\Entities\Country",
                                    "pluck" => ["iso_2_code", "name"],
                                    "default" => "",
                                    "options" => [],
                                    "rules" => "exists:countries,iso_2_code",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ]
                            ]
                        ],
                        [
                            "title" => "Cash On Delivery Payment",
                            "slug" => "cash_on_delivery",
                            "elements" => [
                                [
                                    "title" => "Enabled",
                                    "path" => "payment_methods_cash_on_delivery",
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
                                    "title" => "Title",
                                    "path" => "payment_methods_cash_on_delivery_title",
                                    "type" => "text",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => "Cash On Delivery",
                                    "options" => [],
                                    "rules" => "",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "New Order Status",
                                    "path" => "payment_methods_cash_on_delivery_new_order_status",
                                    "type" => "select",
                                    "provider" => "Modules\Sales\Entities\OrderStatus",
                                    "pluck" => ["slug", "name"],
                                    "default" => "pending",
                                    "options" => [],
                                    "rules" => "exists:order_statuses,slug",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Payment From Applicable Countries",
                                    "path" => "payment_methods_cash_on_delivery_payment_from_applicable_countries",
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
                                    "path" => "payment_methods_cash_on_delivery_payment_from_specific_countries",
                                    "type" => "select",
                                    "provider" => "Modules\Country\Entities\Country",
                                    "pluck" => ["iso_2_code", "name"],
                                    "default" => "",
                                    "options" => [],
                                    "rules" => "exists:countries,iso_2_code",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ]
                            ]
                        ], 
                    ]
                ],
                [
                    "title" => "Delivery Methods",
                    "slug" => "delivery_methods",
                    "subChildren" => [
                        [
                            "title" => "Flat Rate",
                            "slug" => "flat_rate",
                            "elements" => [
                                [
                                    "title" => "Enabled",
                                    "path" => "delivery_methods_flat_rate",
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
                                    "title" => "Title",
                                    "path" => "delivery_methods_flat_rate_title",
                                    "type" => "text",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => "Flat Rate",
                                    "options" => [],
                                    "rules" => "",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Method Name",
                                    "path" => "delivery_methods_flat_rate_method_name",
                                    "type" => "text",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => "Fixed",
                                    "options" => [],
                                    "rules" => "",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Type",
                                    "path" => "delivery_methods_flat_rate_flat_type",
                                    "type" => "select",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => "per_order",
                                    "options" => [
                                        [ "value" => "per_item", "label" => "Per Item" ],
                                        [ "value" => "per_order", "label" => "Per Order" ]
                                    ],
                                    "rules" => "in:per_item,per_order",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Price",
                                    "path" => "delivery_methods_flat_rate_flat_price",
                                    "type" => "text",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => "",
                                    "options" => [],
                                    "rules" => "decimal",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Ship to Applicable Countries",
                                    "path" => "delivery_methods_flat_rate_ship_from_applicable_countries",
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
                                    "title" => "Ship From Specific Countries",
                                    "path" => "delivery_methods_flat_rate_ship_from_specific_countries",
                                    "type" => "select",
                                    "provider" => "Modules\Country\Entities\Country",
                                    "pluck" => ["iso_2_code", "name"],
                                    "default" => "yes",
                                    "options" => [],
                                    "rules" => "exists:countries,iso_2_code",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ]
                            ]
                        ],
                        [
                            "title" => "Free Shipping",
                            "slug" => "free_shipping",
                            "elements" => [
                                [
                                    "title" => "Enabled",
                                    "path" => "delivery_methods_free_shipping",
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
                                    "title" => "Title",
                                    "path" => "delivery_methods_free_shipping_title",
                                    "type" => "text",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => "Flat Rate",
                                    "options" => [],
                                    "rules" => "",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Method Name",
                                    "path" => "delivery_methods_free_shipping_method_name",
                                    "type" => "text",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => "Fixed",
                                    "options" => [],
                                    "rules" => "",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Minimum Order Amount",
                                    "path" => "delivery_methods_free_shipping_minimum_order_amt",
                                    "type" => "text",
                                    "provider" => "",
                                    "pluck" => [],
                                    "default" => 0.00,
                                    "options" => [],
                                    "rules" => "decimal",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ],
                                [
                                    "title" => "Include Tax to Amount",
                                    "path" => "delivery_methods_free_shipping_include_tax_to_amt",
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
                                    "title" => "Ship to Applicable Countries",
                                    "path" => "delivery_methods_free_shipping_ship_from_applicable_countries",
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
                                    "title" => "Ship From Specific Countries",
                                    "path" => "delivery_methods_free_shipping_ship_from_specific_countries",
                                    "type" => "select",
                                    "provider" => "Modules\Country\Entities\Country",
                                    "pluck" => ["iso_2_code", "name"],
                                    "default" => "iso_2_code",
                                    "options" => [],
                                    "rules" => "exists:countries,iso_2_code",
                                    "multiple" => false,
                                    "scope" => "channel",
                                    "is_required" => 0,
                                    "sort_by" => ""
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
