<?php
return [
    [
        "title" => "Feature",
        "slug" => "feature",
        "mainGroups" => [
            [
                "title" => "Section Settings",
                "slug" => "section_settings",
                "type" => "section",
                "groups" => [
                    [
                        "title" => "General",
                        "slug" => "general",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Admin Title",
                                "slug" => "admin-title",
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
                            ],
                            [
                                "title" => "Status",
                                "slug" => "status",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => 1, "label" => "Enabled" ],
                                    [ "value" => 2, "label" => "Disabled" ],
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "in:1,2",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Background",
                        "slug" => "section-background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "section-background-color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "section-background-image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "section-background-position",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => "no-repeat;left top;;", "label" => "Left Top | no-repeat" ],
                                    [ "value" => "repeat;left top;;", "label" => "Left Top | repeat" ],
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "section-background-size",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => "auto", "label" => "Auto" ],
                                    [ "value" => "contain", "label" => "Contain" ],
                                    [ "value" => "cover", "label" => "Cover" ]
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Video Link",
                                "slug" => "section-background-video-link",
                                "hasChildren" => 0,
                                "type" => "link",
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
                        "title" => "Layout",
                        "slug" => "layout",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Padding Top",
                                "slug" => "padding-top",
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
                            ],
                            [
                                "title" => "Padding Bottom",
                                "slug" => "padding-bottom",
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
                            ],
                            [
                                "title" => "Padding Left/Right",
                                "slug" => "padding-left-right",
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
                            ],
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "section-advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "section-custom-classes",
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
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "section-custom-id",
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
                            ],
                        ]
                    ],

                ]
            ],
            [
                "title" => "Row Settings",
                "slug" => "row_settings",
                "type" => "row",
                "groups" => [
                    [
                        "title" => "Background",
                        "slug" => "row-background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "row-background-color",
                                "hasChildren" => 0,
                                "type" => "Colorpicker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "row-background-image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "row-background-position",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => "no-repeat;left top;;", "label" => "Left Top | no-repeat" ],
                                    [ "value" => "repeat;left top;;", "label" => "Left Top | repeat" ],
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "row-background-size",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => "auto", "label" => "Auto" ],
                                    [ "value" => "contain", "label" => "Contain" ],
                                    [ "value" => "cover", "label" => "Cover" ]
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "row-advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "row-custom-classes",
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
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "row-custom-id",
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
                            ],
                        ]
                    ],
                ]
            ],
            [
                "title" => "Module",
                "slug" => "module",
                "type" => "module",
                "subGroups" => [
                    [
                        "title" => "Content",
                        "slug" => "content_module",
                        "column" => "12",
                        "groups" => [
                            [
                                "title" => "Content",
                                "slug" => "sub-content-module",
                                "hasChildren" => 1,
                                "attributes" => [
                                    [
                                        "title" => "Title",
                                        "slug" => "content-module-title",
                                        "hasChildren" => 0,
                                        "type" => "text",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "Enter top padding in <em>px</em>",
                                        "conditions" => [],
                                        "rules" => "",
                                        "is_required" => 0
                                    ],
                                    [
                                        "title" => "Heading Tag",
                                        "slug" => "heading-tag",
                                        "hasChildren" => 0,
                                        "type" => "select",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [
                                            [ "value" => "h1", "label" => "H1" ],
                                            [ "value" => "h2", "label" => "H2" ],
                                            [ "value" => "h3", "label" => "H3" ],
                                            [ "value" => "h4", "label" => "H4" ],
                                            [ "value" => "h5", "label" => "H5" ],
                                            [ "value" => "h6", "label" => "H6" ],
                                        ],
                                        "description" => "Enter bottom padding in <em>px</em>",
                                        "conditions" => [],
                                        "rules" => "in:h1,h2,h3,h4,h5,h6",
                                        "is_required" => 0
                                    ],
                                    [
                                        "title" => "Description",
                                        "slug" => "content-module-description",
                                        "hasChildren" => 0,
                                        "type" => "editor",
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
                            ]
                        ]
                    ],
                    [
                        "title" => "Feature",
                        "slug" => "feature_module",
                        "column" => "12",
                        "groups" => [
                            [
                                "title" => "Feature",
                                "slug" => "sub_feature_module",
                                "hasChildren" => 1,
                                "attributes" => [
                                    [
                                        "title" => "Feature",
                                        "slug" => "feature-repeater",
                                        "hasChildren" => 1,
                                        "type" => "repeater",
                                        "conditions" => [],
                                        "description" => "",
                                        "rules" => "array",
                                        "is_required" => 0,
                                        "attributes" => [
                                            [
                                                [
                                                    "title" => "Icon",
                                                    "slug" => "feature-repeater-icon",
                                                    "hasChildren" => 0,
                                                    "type" => "file",
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
                                                    "title" => "Title",
                                                    "slug" => "feature-repeater-title",
                                                    "hasChildren" => 0,
                                                    "type" => "text",
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
                                                    "title" => "Description",
                                                    "slug" => "feature-repeater-description",
                                                    "hasChildren" => 0,
                                                    "type" => "textarea",
                                                    "provider" => "",
                                                    "pluck" => [],
                                                    "default" => "",
                                                    "options" => [],
                                                    "conditions" => [],
                                                    "description" => "",
                                                    "rules" => "",
                                                    "is_required" => 0
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "title" => "Items per row",
                                        "slug" => "feature-items-per-row",
                                        "hasChildren" => 0,
                                        "type" => "select",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [
                                            [ "value" => 2, "label" => "Two" ],
                                            [ "value" => 3, "label" => "Three" ],
                                            [ "value" => 4, "label" => "Four" ]
                                        ],
                                        "conditions" => [],
                                        "description" => "",
                                        "rules" => "in:2,3,4",
                                        "is_required" => 1
                                    ],
        
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        "title" => "Leading Text",
        "slug" => "leading_text",
        "mainGroups" => [
            [
                "title" => "Section Settings",
                "slug" => "section_settings",
                "type" => "section",
                "groups" => [
                    [
                        "title" => "General",
                        "slug" => "general",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Admin Title",
                                "slug" => "admin-title",
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
                            ],
                            [
                                "title" => "Status",
                                "slug" => "status",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => 1, "label" => "Enabled" ],
                                    [ "value" => 2, "label" => "Disabled" ],
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "in:1,2",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Background",
                        "slug" => "section-background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "section-background-color",
                                "hasChildren" => 0,
                                "type" => "Colorpicker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "section-background-image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "section-background-position",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => "no-repeat;left top;;", "label" => "Left Top | no-repeat" ],
                                    [ "value" => "repeat;left top;;", "label" => "Left Top | repeat" ],
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "section-background-size",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => "auto", "label" => "Auto" ],
                                    [ "value" => "contain", "label" => "Contain" ],
                                    [ "value" => "cover", "label" => "Cover" ]
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Video Link",
                                "slug" => "section-background-video-link",
                                "hasChildren" => 0,
                                "type" => "link",
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
                        "title" => "Layout",
                        "slug" => "layout",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Padding Top",
                                "slug" => "padding-top",
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
                            ],
                            [
                                "title" => "Padding Bottom",
                                "slug" => "padding-bottom",
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
                            ],
                            [
                                "title" => "Padding Left/Right",
                                "slug" => "padding-left-right",
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
                            ],
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "section-advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "section-custom-classes",
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
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "section-custom-id",
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
                            ],
                        ]
                    ],

                ]
            ],
            [
                "title" => "Row Settings",
                "slug" => "row_settings",
                "type" => "row",
                "groups" => [
                    [
                        "title" => "Background",
                        "slug" => "row-background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "row-background-color",
                                "hasChildren" => 0,
                                "type" => "Colorpicker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "row-background-image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "row-background-position",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => "no-repeat;left top;;", "label" => "Left Top | no-repeat" ],
                                    [ "value" => "repeat;left top;;", "label" => "Left Top | repeat" ],
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "row-background-size",
                                "hasChildren" => 0,
                                "type" => "select",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [
                                    [ "value" => "auto", "label" => "Auto" ],
                                    [ "value" => "contain", "label" => "Contain" ],
                                    [ "value" => "cover", "label" => "Cover" ]
                                ],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 1
                            ]
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "row-advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "row-custom-classes",
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
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "row-custom-id",
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
                            ],
                        ]
                    ],
                ]
            ],
            [
                "title" => "Module",
                "slug" => "module",
                "type" => "module",
                "subGroups" => [
                    [
                        "title" => "Content",
                        "slug" => "content_module",
                        "column" => "12",
                        "groups" => [
                            [
                                "title" => "Content",
                                "slug" => "sub-content-module",
                                "hasChildren" => 1,
                                "attributes" => [
                                    [
                                        "title" => "Title",
                                        "slug" => "content-module-title",
                                        "hasChildren" => 0,
                                        "type" => "text",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "Enter top padding in <em>px</em>",
                                        "conditions" => [],
                                        "rules" => "",
                                        "is_required" => 0
                                    ],
                                    [
                                        "title" => "SubTitle",
                                        "slug" => "content-module-sub-title",
                                        "hasChildren" => 0,
                                        "type" => "text",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "Enter bottom padding in <em>px</em>",
                                        "conditions" => [],
                                        "rules" => "",
                                        "is_required" => 0
                                    ],
                                    [
                                        "title" => "Content",
                                        "slug" => "content-module-content",
                                        "hasChildren" => 0,
                                        "type" => "editor",
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
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
