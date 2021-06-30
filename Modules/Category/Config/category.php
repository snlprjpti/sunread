<?php 
return [
    "attributes" => [
        "general" => [
            "title" => "General Details",
            "elements" => [
                [
                    "title" => "name",
                    "type" => "text",
                    "scope" => "store",
                    "rules" => "",
                    "is_required" => 1,
                    "value" => ""
                ],
                [
                    "title" => "status",
                    "type" => "boolean",
                    "scope" => "store",
                    "rules" => "boolean",
                    "is_required" => 1,
                    "value" => ""
                ],
                [
                    "title" => "include_in_menu",
                    "type" => "boolean",
                    "scope" => "store",
                    "rules" => "boolean",
                    "is_required" => 0,
                    "value" => ""
                ]
            ]
        ],
        "content" => [
            "title" => "Content",
            "elements" => [
                [
                    "title" => "image",
                    "type" => "file",
                    "scope" => "store",
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0,
                    "value" => ""
                ],
                [
                    "title" => "description",
                    "type" => "textarea",
                    "scope" => "store",
                    "rules" => "nullable",
                    "is_required" => 0,
                    "value" => ""
                ]
            ]
        ],
        "display_settings" => [
            "title" => "Display Settings",
            "elements" => []
        ],
        "search_engine_optimization" => [
            "title" => "Search Engine Optimization",
            "elements" => [
                [
                    "title" => "meta_title",
                    "type" => "text",
                    "scope" => "website",
                    "rules" => "nullable",
                    "is_required" => 1,
                    "value" => ""
                ],
                [
                    "title" => "meta_keywords",
                    "type" => "text",
                    "scope" => "channel",
                    "rules" => "nullable",
                    "is_required" => 1,
                    "value" => ""
                ],
                [
                    "title" => "meta_description",
                    "type" => "textarea",
                    "scope" => "store",
                    "rules" => "nullable",
                    "is_required" => 1,
                    "value" => ""
                ]
            ]
        ],
    ]
];