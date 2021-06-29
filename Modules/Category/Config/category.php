<?php 
return [
    "attributes" => [
        "general" => [
            "title" => "General Details",
            "elements" => [
                [
                    "title" => "name",
                    "scope" => "store",
                    "rules" => "",
                    "is_required" => 1,
                    "value" => ""
                ],
                [
                    "title" => "status",
                    "scope" => "store",
                    "rules" => "boolean",
                    "is_required" => 1,
                    "value" => ""
                ],
                [
                    "title" => "include_in_menu",
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
                    "scope" => "store",
                    "rules" => "mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0,
                    "value" => ""
                ],
                [
                    "title" => "description",
                    "scope" => "store",
                    "rules" => "nullable",
                    "is_required" => 0,
                    "value" => ""
                ]
            ]
        ],
        "search_engine_optimization" => [
            "title" => "Search Engine Optimization",
            "elements" => [
                [
                    "title" => "meta_title",
                    "scope" => "website",
                    "rules" => "nullable",
                    "is_required" => 1,
                    "value" => ""
                ],
                [
                    "title" => "meta_keywords",
                    "scope" => "channel",
                    "rules" => "nullable",
                    "is_required" => 1,
                    "value" => ""
                ],
                [
                    "title" => "meta_description",
                    "scope" => "store",
                    "rules" => "nullable",
                    "is_required" => 1,
                    "value" => ""
                ]
            ]
        ],
    ]
];