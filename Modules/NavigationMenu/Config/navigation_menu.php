<?php
return [
    "attributes" => [
        "general" => [
            "title" => "General Details",
            "elements" => [
                [
                    "title" => "Title",
                    "slug" => "title",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 1
                ],
                [
                    "title" => "Type",
                    "slug" => "type",
                    "type" => "select",
                    "value" => "",
                    "scope" => "website",
                    "options" => [
                        [ "value" => "category", "label" => "Category" ],
                        [ "value" => "page", "label" => "Page" ],
                        [ "value" => "custom", "label" => "Custom" ]
                    ],
                    "rules" => "string",
                    "is_required" => 1
                ],
                [
                    "title" => "Type Id",
                    "slug" => "type_id",
                    "type" => "select",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "integer",
                    "is_required" => 0
                ],
                [
                    "title" => "Custom Link",
                    "slug" => "custom_link",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Additional Data",
                    "slug" => "additional_data",
                    "type" => "json",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "json",
                    "is_required" => 0
                ],
                [
                    "title" => "Order",
                    "slug" => "order",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "integer",
                    "is_required" => 1
                ],
                [
                    "title" => "Status",
                    "slug" => "status",
                    "type" => "select",
                    "value" => "",
                    "scope" => "website",
                    "options" => [
                        [ "value" => "1", "label" => "Enabled" ],
                        [ "value" => "0", "label" => "Disabled" ]
                    ],
                    "rules" => "nullable|in:0,1",
                    "is_required" => 0
                ],
            ]
        ],
        "additional_data" => [
            "title" => "Additional Data",
            "elements" => [
                [
                    "title" => "Background Type",
                    "slug" => "background_type",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Background Image",
                    "slug" => "background_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png,gif",
                    "is_required" => 0
                ],
                [
                    "title" => "Background Video Type",
                    "slug" => "background_video_type",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Background Video",
                    "slug" => "background_video",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Background Overlay Color",
                    "slug" => "background_overlay_color",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
            ]
        ]
    ],
    "locations" => [
        "title" => "Menu Location",
        "elements" => [
            [
                "label" => "Footer",
                "slug" => "footer",
            ],
            [
                "label" => "Primary",
                "slug" => "primary",
            ],
            [
                "label" => "Full Screen",
                "slug" => "full_screen",
            ],
        ]
    ],
    "absolute_path" => [
        "title" => "general.elements.0",
        "type" => "general.elements.1",
        "type_id" => "general.elements.2",
        "type_id" => "general.elements.4",
        "custom_link" => "general.elements.5",
        "order" => "general.elements.6",
        "additional_data" => "general.additional.0",
    ]
];
