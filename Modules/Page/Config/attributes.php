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
                        "slug" => "banner/shape-section/top-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Left Image",
                        "slug" => "banner/shape-section/left-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Right Image",
                        "slug" => "banner/shape-section/right-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                ]
            ],
            [
                "title" => "Has Overlay",
                "slug" => "banner/has-overlay",
                "hasChildren" => 0,
                "type" => "radio",
                "provider" => "",
                "pluck" => [],
                "default" => "1",
                "options" => [ 
                    [ "value" => 1, "label" => "Yes" ],
                    [ "value" => 0, "label" => "No" ]
                ],
                "rules" => "nullable|in:0,1",
                "is_required" => 0
            ],
            [
                "title" => "Background Image",
                "slug" => "banner/background-image",
                "hasChildren" => 0,
                "type" => "file",
                "provider" => "",
                "pluck" => [],
                "default" => "",
                "options" => [],
                "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
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
                        "slug" => "banner/banner-content/title",
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
                        "slug" => "banner/banner-content/content",
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
                        "slug" => "content/shape-section/top-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Left Image",
                        "slug" => "content/shape-section/left-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Right Image",
                        "slug" => "content/shape-section/right-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                ]
            ],
            [
                "title" => "Has Overlay",
                "slug" => "content/has-overlay",
                "hasChildren" => 0,
                "type" => "radio",
                "provider" => "",
                "pluck" => [],
                "default" => "1",
                "options" => [ 
                    [ "value" => 1, "label" => "Yes" ],
                    [ "value" => 0, "label" => "No" ]
                ],
                "rules" => "nullable|in:0,1",
                "is_required" => 0
            ],
            [
                "title" => "Background Image",
                "slug" => "content/background-image",
                "hasChildren" => 0,
                "type" => "file",
                "provider" => "",
                "pluck" => [],
                "default" => "",
                "options" => [],
                "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
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
                        "slug" => "content/banner-content/title",
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
                        "slug" => "content/banner-content/content",
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