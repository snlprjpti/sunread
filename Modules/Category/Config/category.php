<?php 
return [
    "attributes" => [
        [
            "title" => "name",
            "scope" => "store",
            "rules" => "",
            "is_required" => 1
        ],
        [
            "title" => "image",
            "scope" => "store",
            // "rules" => "mimes:jpeg,jpg,bmp,png",
            "rules" => "",
            "is_required" => 1
        ],
        [
            "title" => "description",
            "scope" => "store",
            "rules" => "nullable",
            "is_required" => 0
        ],
        [
            "title" => "meta_title",
            "scope" => "store",
            "rules" => "nullable",
            "is_required" => 1
        ],
        [
            "title" => "meta_keywords",
            "scope" => "store",
            "rules" => "nullable",
            "is_required" => 1
        ],
        [
            "title" => "meta_description",
            "scope" => "store",
            "rules" => "nullable",
            "is_required" => 1
        ],
        [
            "title" => "status",
            "scope" => "store",
            "rules" => "boolean",
            "is_required" => 1
        ],
        [
            "title" => "include_in_menu",
            "scope" => "store",
            "rules" => "boolean",
            "is_required" => 1
        ]
    ]
];