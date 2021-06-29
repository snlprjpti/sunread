<?php 
return [
    "attributes" => [
        "name" => [
            "title" => "name",
            "scope" => "website",
            "rules" => "",
            "is_required" => 1
        ],
        "description" => [
            "title" => "description",
            "scope" => "store",
            "rules" => "nullable",
            "is_required" => 0
        ],
        "meta_title" => [
            "title" => "meta_title",
            "scope" => "website",
            "rules" => "nullable",
            "is_required" => 1
        ],
        "meta_keywords" => [
            "title" => "meta_keywords",
            "scope" => "channel",
            "rules" => "nullable",
            "is_required" => 1
        ],
        "meta_description" => [
            "title" => "meta_description",
            "scope" => "store",
            "rules" => "nullable",
            "is_required" => 1
        ],
        "status" => [
            "title" => "status",
            "scope" => "channel",
            "rules" => "boolean",
            "is_required" => 1
        ],
        "include_in_menu" => [
            "title" => "include_in_menu",
            "scope" => "website",
            "rules" => "boolean",
            "is_required" => 1
        ]
    ]
];