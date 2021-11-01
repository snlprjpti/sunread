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
                    "title" => "Slug",
                    "slug" => "slug",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 0
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
        "content" => [
            "title" => "Content",
            "elements" => [
                [
                    "title" => "Thumbnail",
                    "slug" => "thumbnail",
                    "type" => "file",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                    "is_required" => 1
                ],
                [
                    "title" => "Header Content",
                    "slug" => "header_content",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 1
                ],
                [
                    "title" => "Opening hours",
                    "slug" => "opening_hours",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 1
                ],
                [
                    "title" => "Address",
                    "slug" => "address",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 1
                ],
                [
                    "title" => "Contact",
                    "slug" => "contact",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "options" => [],
                    "rules" => "string",
                    "is_required" => 1
                ],
            ]
        ],
        "location" => [
            "title" => "Location",
            "elements" => [
                [
                    "title" => "Latitude",
                    "slug" => "latitude",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "numeric",
                    "is_required" => 0
                ],
                [
                    "title" => "Longitude",
                    "slug" => "longitude",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "options" => [],
                    "rules" => "numeric",
                    "is_required" => 0
                ],
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
                    "scope" => "website",
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
                    "scope" => "website",
                    "options" => [],
                    "rules" => "mimes:jpeg,jpg,bmp,png",
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
        "title" => "general.elements.0",
        "slug" => "general.elements.1",
        "status" => "general.elements.2",
        "thumbnail" => "content.elements.0",
        "header_content" => "content.elements.1",
        "opening_hours" => "content.elements.2",
        "address" => "content.elements.3",
        "contact" => "content.elements.4",
        "latitude" => "location.elements.0",
        "longitude" => "location.elements.1",
        "background_type" => "hero_banner.elements.0",
        "background_image" => "hero_banner.elements.1",
        "meta_title" => "search_engine_optimization.elements.0",
        "meta_keywords" => "search_engine_optimization.elements.1",
        "meta_description" => "search_engine_optimization.elements.2"
    ]
];
