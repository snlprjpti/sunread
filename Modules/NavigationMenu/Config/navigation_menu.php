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
                    "has_condition" => 1,
                    "rules" => "string|in:category,page,custom",
                    "is_required" => 1
                ],
                [
                    "title" => "Type Id",
                    "slug" => "type_id",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "condition" => [
                        "field" => 'type',
                        "value" => 'category,page'
                    ],
                    "options" => [],
                    "rules" => "required_if:items.type.value,page,category|integer",
                    "is_required" => 0
                ],
                [
                    "title" => "Custom Link",
                    "slug" => "custom_link",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "condition" => [
                        "field" => 'type',
                        "value" => 'custom'
                    ],
                    "options" => [],
                    "rules" => "required_if:items.type.value,custom|string",
                    "is_required" => 0
                ],
                [
                    "title" => "Order",
                    "slug" => "order",
                    "type" => "number",
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
                    "rules" => "in:0,1",
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
                    "type" => "select",
                    "value" => "",
                    "scope" => "website",
                    "has_condition" => 1,
                    "options" => [
                        [ "value" => "image", "label" => "Image" ],
                        [ "value" => "video", "label" => "Video" ]
                    ],
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
                    "condition" => [
                        "field" => 'background_type',
                        "value" => 'image'
                    ],
                    "rules" => "mimes:jpeg,jpg,bmp,png,gif",
                    "is_required" => 0
                ],
                [
                    "title" => "Background Video Type",
                    "slug" => "background_video_type",
                    "type" => "select",
                    "value" => "",
                    "scope" => "website",
                    "options" => [
                        ["value" => "wistia", "label" => "Wistia"],
                        ["value" => "youtube", "label" => "YouTube"],
                        ["value" => "selfhosted", "label" => "Self Hosted"],
                        ["value" => "vimeo", "label" => "Vimeo"],
                    ],
                    "condition" => [
                        "field" => 'background_type',
                        "value" => 'video'
                    ],
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
                    "condition" => [
                        "field" => 'background_type',
                        "value" => 'video'
                    ],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Background Overlay Color",
                    "slug" => "background_overlay_color",
                    "type" => "color_picker",
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
    ],
    "absolute_path" => [
        "title" => "general.elements.0",
        "type" => "general.elements.1",
        "type_id" => "general.elements.2",
        "custom_link" => "general.elements.3",
        "order" => "general.elements.4",
        "status" => "general.elements.5",
        "background_type" => "additional_data.elements.0",
        "background_image" => "additional_data.elements.1",
        "background_video_type" => "additional_data.elements.2",
        "background_video" => "additional_data.elements.3",
        "background_overlay_color" => "additional_data.elements.4",
    ]
];
