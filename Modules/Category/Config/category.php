<?php
return [
    "attributes" => [
        "general" => [
            "title" => "General Details",
            "elements" => [
                [
                    "title" => "Name",
                    "slug" => "name",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 1
                ],
                [
                    "title" => "Slug",
                    "slug" => "slug",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0

                ],
                [
                    "title" => "Status",
                    "slug" => "status",
                    "type" => "select",
                    "value" => "",
                    "scope" => "store",
                    "options" => [
                        [ "value" => "1", "label" => "Enabled" ],
                        [ "value" => "0", "label" => "Disabled" ]
                    ],
                    "rules" => "nullable|in:0,1",
                    "is_required" => 0
                ],
                [
                    "title" => "Include In Menu",
                    "slug" => "include_in_menu",
                    "type" => "select",
                    "value" => "",
                    "scope" => "store",
                    "options" => [
                        [ "value" => "1", "label" => "Yes" ],
                        [ "value" => "0", "label" => "No" ]
                    ],
                    "rules" => "nullable|in:0,1",
                    "is_required" => 0

                ]
            ]
        ],
        "content" => [
            "title" => "Content",
            "elements" => [
                [
                    "title" => "Image",
                    "slug" => "image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Description",
                    "slug" => "description",
                    "type" => "textarea",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "nullable|string",
                    "is_required" => 0

                ]
            ]
        ],
        "hero_banner" => [
            "title" => "Hero Banner",
            "elements" => [
                [
                    "title" => "Background Type",
                    "slug" => "background_type",
                    "type" => "select",
                    "value" => "",
                    "scope" => "store",
                    "options" => [
                        [ "value" => "image", "label" => "Image" ],
                        [ "value" => "video", "label" => "Video" ]
                    ],
                    "rules" => "in:image,video",
                    "multiple" => false,
                    "is_required" => 0
                ],
                [
                    "title" => "Background Image",
                    "slug" => "background_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Youtube Link",
                    "slug" => "youtube_link",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                                [
                    "title" => "Gradient Color",
                    "slug" => "gradient_color",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Title",
                    "slug" => "hero_banner_title",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Content",
                    "slug" => "hero_banner_content",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Readmore Label",
                    "slug" => "readmore_label",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
                [
                    "title" => "Readmore Link",
                    "slug" => "readmore_link",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
                ],
            ]
        ],
        "usp_banner_1" => [
            "title" => "First Unique Selling Points Banner",
            "elements" => [
                [
                    "title" => "First Image",
                    "slug" => "usp_banner_1_first_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Second Image",
                    "slug" => "usp_banner_1_second_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Third Image",
                    "slug" => "usp_banner_1_third_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Placement",
                    "slug" => "usp_banner_1_placement",
                    "type" => "number",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "integer",
                    "is_required" => 0
                ],
            ]
        ],
        "usp_banner_2" => [
            "title" => "Second Unique Selling Points Banner",
            "elements" => [
                [
                    "title" => "First Image",
                    "slug" => "usp_banner_2_first_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Second Image",
                    "slug" => "usp_banner_2_second_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Third Image",
                    "slug" => "usp_banner_2_third_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Placement",
                    "slug" => "usp_banner_2_placement",
                    "type" => "number",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "integer",
                    "is_required" => 0
                ],
            ]
        ],
        "usp_banner_3" => [
            "title" => "Third Unique Selling Points Banner",
            "elements" => [
                [
                    "title" => "First Image",
                    "slug" => "usp_banner_3_first_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Second Image",
                    "slug" => "usp_banner_3_second_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Third Image",
                    "slug" => "usp_banner_3_third_image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0
                ],
                [
                    "title" => "Placement",
                    "slug" => "usp_banner_3_placement",
                    "type" => "number",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "integer",
                    "is_required" => 0
                ],
            ]
        ],
        "search_engine_optimization" => [
            "title" => "Search Engine Optimization",
            "elements" => [
                [
                    "title" => "Meta Title",
                    "slug" => "meta_title",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "nullable",
                    "is_required" => 0

                ],
                [
                    "title" => "Meta KeyWords",
                    "slug" => "meta_keywords",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "nullable",
                    "is_required" => 0

                ],
                [
                    "title" => "Meta Description",
                    "slug" => "meta_description",
                    "type" => "textarea",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "nullable",
                    "is_required" => 0

                ]
            ]
        ],
    ],
    "absolute_path" => [
        "name" => "general.elements.0",
        "slug" => "general.elements.1",
        "status" => "general.elements.2",
        "include_in_menu" => "general.elements.3",
        "image" => "content.elements.0",
        "description" => "content.elements.1",
        "background_type" => "hero_banner.elements.0",
        "background_image" => "hero_banner.elements.1",
        "youtube_link" => "hero_banner.elements.2",
        "gradient_color" => "hero_banner.elements.3",
        "hero_banner_title" => "hero_banner.elements.4",
        "hero_banner_content" => "hero_banner.elements.5",
        "readmore_label" => "hero_banner.elements.6",
        "readmore_link" => "hero_banner.elements.7",
        "usp_banner_1_first_image" => "usp_banner_1.elements.0",
        "usp_banner_1_second_image" => "usp_banner_1.elements.1",
        "usp_banner_1_third_image" => "usp_banner_1.elements.2",
        "usp_banner_1_placement" => "usp_banner_1.elements.3",
        "usp_banner_2_first_image" => "usp_banner_2.elements.0",
        "usp_banner_2_second_image" => "usp_banner_2.elements.1",
        "usp_banner_2_third_image" => "usp_banner_2.elements.2",
        "usp_banner_2_placement" => "usp_banner_2.elements.3",
        "usp_banner_3_first_image" => "usp_banner_3.elements.0",
        "usp_banner_3_second_image" => "usp_banner_3.elements.1",
        "usp_banner_3_third_image" => "usp_banner_3.elements.2",
        "usp_banner_3_placement" => "usp_banner_3.elements.3",
        "meta_title" => "search_engine_optimization.elements.0",
        "meta_keywords" => "search_engine_optimization.elements.1",
        "meta_description" => "search_engine_optimization.elements.2"
    ]
];
