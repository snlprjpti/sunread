<?php
return [
    [
        "title" => "Banner",
        "slug" => "banner",
        "groups" => [
            [
                "title" => "Shape section",
                "slug" => "shape-section",
                "hasChildren" => 1,
                "attributes" => [
                    [
                        "title" => "Top Image",
                        "slug" => "top-image",
                        "hasChildren" => 0,
                        "type" => "text",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "description" => "",
                        "conditions" => [],
                        "rules" => "",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Left Image",
                        "slug" => "left-image",
                        "hasChildren" => 0,
                        "type" => "text",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "description" => "",
                        "conditions" => [],
                        "rules" => "",
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
                        "description" => "",
                        "conditions" => [],
                        "rules" => "mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ]
                ]
            ],
            [
                "title" => "Style",
                "slug" => "style",
                "hasChildren" => 1,
                "attributes" => [
                    [
                        "title" => "Padding Top",
                        "slug" => "padding-top",
                        "hasChildren" => 0,
                        "type" => "number",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "description" => "Enter top padding in <em>px</em>",
                        "conditions" => [],
                        "rules" => "numeric",
                        "is_required" => 1
                    ],
                    [
                        "title" => "Padding Bottom",
                        "slug" => "padding-bottom",
                        "hasChildren" => 0,
                        "type" => "number",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "description" => "Enter bottom padding in <em>px</em>",
                        "conditions" => [],
                        "rules" => "numeric",
                        "is_required" => 1
                    ],
                    [
                        "title" => "Class",
                        "slug" => "css-class",
                        "hasChildren" => 0,
                        "type" => "text",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "description" => "",
                        "conditions" => [],
                        "rules" => "",
                        "is_required" => 1
                    ]
                ]
            ],
            [
                "title" => "Content",
                "slug"	=> "content",
                "hasChildren" => 1,
                "attributes" => [
                    [
                        "title" => "Backgroud Type",
                        "slug" => "backgroud-type",
                        "hasChildren" => 0,
                        "type" => "select",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "image",
                        "options" => [ 
                            [ "value" => "image", "label" => "Image" ],
                            [ "value" => "video", "label" => "Video" ]
                        ],
                        "description" => "",
                        "conditions" => [],
                        "rules" => "in:image,video",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Backgroud Image",
                        "slug" => "backgroud-image",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "description" => "",
                        "conditions" => [
                            "operator"	=> "AND",
                            "condition"	=> [
                                [
                                    "backgroud-type" => "image"
                                ]
                            ]
                        ],
                        "rules" => "mimes:jpeg,jpg,bmp,png",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Video Type",
                        "slug" => "video-type",
                        "hasChildren" => 0,
                        "type" => "select",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [ 
                            [ "value" => "youtube", "label" => "Youtube" ],
                            [ "value" => "vimeo", "label" => "Vimeo" ],
                            [ "value" => "hosted", "label" => "Self Hosted" ]
                        ],
                        "description" => "",
                        "conditions" => [
                            "operator"	=> "AND",
                            "condition"	=> [
                                [
                                    "backgroud-type" => "video"
                                ]
                            ]
                        ],
                        "rules" => "in:youtube,vimeo,hosted",
                        "multiple" => false,
                        "is_required" => 1
                    ],
                    [
                        "title" => "Video Link",
                        "slug" => "video-link",
                        "hasChildren" => 0,
                        "type" => "text",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "description" => "",
                        "conditions" => [
                            "operator" => "AND",
                            "condition"	=> [
                                [
                                    "backgroud-type" => "video",
                                    "video-type" => "youtube"
                                ],
                                [
                                    "backgroud-type" => "video",
                                    "video-type" => "vimeo"
                                ]
                            ]
                        ],
                        "rules" => "",
                        "is_required" => 1
                    ],
                    [
                        "title" => "Background Video",
                        "slug" => "background-video",
                        "hasChildren" => 0,
                        "type" => "file",
                        "provider" => "",
                        "pluck" => [],
                        "default" => "",
                        "options" => [],
                        "description" => "",
                        "conditions" => [
                            "operator"	=> "AND",
                            "condition"	=> [
                                [
                                    "backgroud-type" => "video",
                                    "video-type" => "hosted"
                                ]
                            ]
                        ],
                        "rules" => "mimes:mp4,x-flv,x-mpegURL,MP2T,3gpp,quicktime,x-msvideo,x-ms-wmv",
                        "multiple" => false,
                        "is_required" => 0              
                    ],
                    [
                        "title" => "Buttons",
                        "slug" => "buttons",
                        "hasChildren" => 1,
                        "type" => "repeater",
                        "conditions" => [],
                        "description" => "",
                        "rules" => "array",
                        "is_required" => 1,
                        "attributes" => [
                            [
                                "title" => "Button Link",
                                "slug" => "button-link",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "conditions" => [],
                                "description" => "",
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Button Image",
                                "slug" => "button-image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "conditions" => [],
                                "description" => "",
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Button Color",
                                "slug" => "button-color",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "conditions" => [],
                                "description" => "",
                                "rules" => "",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Banner Editor",
                        "slug" => "banner-editor",
                        "hasChildren" => 1,
                        "type" => "normal",
                        "conditions" => [],
                        "description" => "",
                        "rules" => "array",
                        "is_required" => 1,
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
                                "conditions" => [],
                                "description" => "",
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Content Image",
                                "slug" => "content-image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "conditions" => [],
                                "description" => "",
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ]
                        ]
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
                "description" => "",
                "conditions" => [],
                "rules" => "boolean",
                "is_required" => 0
            ]
        ]
    ]
];
?>