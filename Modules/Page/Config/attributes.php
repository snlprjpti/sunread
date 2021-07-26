<?php
return [
    [
        "title" => "Banner",
        "slug" => "banner",
        "attributes" => [
            [
                "title" => "Shape section",
                "slug" => "shape-section",
                "hasChildren" => 1,
                "attributes" => [
                    [
                        "title" => "Top Image",
                        "slug" => "top-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Left Image",
                        "slug" => "left-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Right Image",
                        "slug" => "right-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                ]
            ],
            [
                "title" => "Has Overlay",
                "slug" => "has-overlay",
                "hasChildren" => 0,
                "type" => "radio",
                "provider" => "",
                "pluck" => [],
                "default" => "1",
                "options" => [ 
                    [ "value" => 1, "label" => "Yes" ],
                    [ "value" => 0, "label" => "No" ]
                ],
                "rules" => "in:0,1",
                "is_required" => 0
            ],
            [
                "title" => "Background Image",
                "slug" => "background-image",
                "hasChildren" => 0,
                "type" => "file",
                "provider" => "",
                "pluck" => [],
                "default" => "",
                "options" => [],
                "rules" => "mimes:jpeg,jpg,bmp,png",
                "multiple" => false,
                "is_required" => 1
            ],
            [
                "title" => "Banner Content",
                "slug" => "banner-content",
                "hasChildren" => 1,
                "attributes" => [
                    [
                        "title" => "Title",
                        "slug" => "title",
                        "hasChildren" => 0,
                        "type" => "text",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "",
                        "is_required" => 1
                    ],
                    [
                        "title" => "Content",
                        "slug" => "content",
                        "hasChildren" => 0,
                        "type" => "textarea",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "",
                        "is_required" => 1
                    ]
                ]
            ],
        ]
    ],
    [
        "title" => "Content",
        "slug" => "content",
        "attributes" => [
            [
                "title" => "Shape section",
                "slug" => "shape-section",
                "hasChildren" => 1,
                "attributes" => [
                    [
                        "title" => "Top Image",
                        "slug" => "top-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Left Content",
                        "slug" => "left-content",
                        "hasChildren" => 0,
                        "type" => "text",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Right Content",
                        "slug" => "right-content",
                        "hasChildren" => 0,
                        "type" => "text",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                ]
            ],
            [
                "title" => "Has Overlay",
                "slug" => "has-overlay",
                "hasChildren" => 0,
                "type" => "radio",
                "provider" => "",
                "pluck" => [],
                "default" => "1",
                "options" => [ 
                    [ "value" => 1, "label" => "Yes" ],
                    [ "value" => 0, "label" => "No" ]
                ],
                "rules" => "in:0,1",
                "is_required" => 0
            ],
            [
                "title" => "Background Content",
                "slug" => "background-content",
                "hasChildren" => 0,
                "type" => "text",
                "provider" => "",
                "pluck" => [],
                "default" => "",
                "options" => [],
                "rules" => "",
                "multiple" => false,
                "is_required" => 1
            ],
            [
                "title" => "Banner Content",
                "slug" => "banner-content",
                "hasChildren" => 1,
                "attributes" => [
                    [
                        "title" => "Title",
                        "slug" => "title",
                        "hasChildren" => 0,
                        "type" => "text",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "",
                        "is_required" => 1
                    ],
                    [
                        "title" => "Content",
                        "slug" => "content",
                        "hasChildren" => 0,
                        "type" => "textarea",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "",
                        "is_required" => 1
                    ]
                ]
            ],
        ]
    ],
];
?>