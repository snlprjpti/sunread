<?php

return [
    "name" => "sales",
    "configuration_merge" => true,
    "configuration" => [
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
                        config("paymentbanktransfer"),
                        config("paymentcashondelivery"),
                        config("paymentklarna"),
                        config("paymentklarna.api_configuration"),
                        config("paymentklarna.klarna_design"),
                        config("paymentadyen"),
                        config("paymentadyen.api_configuration")
                    ]
                ],
                [
                    "title" => "Delivery Methods",
                    "slug" => "delivery_methods",
                    "subChildren" => [
                        config("deliveryflatrate"),
                        config("deliveryfreeshipping")
                    ]
                ]
            ]
        ]
    ]
];
