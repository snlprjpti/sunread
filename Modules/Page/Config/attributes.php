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
                                "slug" => "admin_title",
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
                        "slug" => "section_background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "section_background_color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "section_background_image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "section_background_position",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "section_background_size",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Video Link",
                                "slug" => "section_background_video_link",
                                "hasChildren" => 0,
                                "type" => "link",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "slug" => "padding_top",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Padding Bottom",
                                "slug" => "padding_bottom",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Padding Left/Right",
                                "slug" => "padding_left_right",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "section_advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "section_custom_classes",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "section_custom_id",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                        "slug" => "row_background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "row_background_color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "row_background_image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "row_background_position",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "row_background_size",
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
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "row_advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "row_custom_classes",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "row_custom_id",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "slug" => "sub_content_module",
                                "hasChildren" => 1,
                                "attributes" => [
                                    [
                                        "title" => "Title",
                                        "slug" => "content_module_title",
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
                                        "slug" => "heading_tag",
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
                                        "slug" => "content_module_description",
                                        "hasChildren" => 0,
                                        "type" => "editor",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "",
                                        "conditions" => [],
                                        "rules" => "",
                                        "is_required" => 0
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
                                        "slug" => "feature_repeater",
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
                                                    "slug" => "feature_repeater_icon",
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
                                                    "title" => "Title",
                                                    "slug" => "feature_repeater_title",
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
                                                    "title" => "Description",
                                                    "slug" => "feature_repeater_description",
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
                                        "slug" => "feature_items_per_row",
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
                                "slug" => "admin_title",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Background",
                        "slug" => "section_background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "section_background_color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "section_background_image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "section_background_position",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "section_background_size",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Video Link",
                                "slug" => "section_background_video_link",
                                "hasChildren" => 0,
                                "type" => "link",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "slug" => "padding_top",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Padding Bottom",
                                "slug" => "padding_bottom",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Padding Left/Right",
                                "slug" => "padding_left_right",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "section_advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "section_custom_classes",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "section_custom_id",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                        "slug" => "row_background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "row_background_color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "row_background_image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "row_background_position",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "row_background_size",
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
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "row_advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "row_custom_classes",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "row_custom_id",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "slug" => "sub_content_module",
                                "hasChildren" => 1,
                                "attributes" => [
                                    [
                                        "title" => "Title",
                                        "slug" => "content_module_title",
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
                                        "title" => "Sub Title",
                                        "slug" => "content_module_sub_title",
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
                                        "slug" => "content_module_content",
                                        "hasChildren" => 0,
                                        "type" => "editor",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "",
                                        "conditions" => [],
                                        "rules" => "",
                                        "is_required" => 0
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        "title" => "Featured Products",
        "slug" => "featured_products",
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
                                "slug" => "admin_title",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Background",
                        "slug" => "section_background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "section_background_color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "section_background_image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "section_background_position",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "section_background_size",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Video Link",
                                "slug" => "section_background_video_link",
                                "hasChildren" => 0,
                                "type" => "link",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "slug" => "padding_top",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Padding Bottom",
                                "slug" => "padding_bottom",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Padding Left/Right",
                                "slug" => "padding_left_right",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "section_advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "section_custom_classes",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "section_custom_id",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                        "slug" => "row_background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "row_background_color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "row_background_image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "row_background_position",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "row_background_size",
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
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "row_advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "row_custom_classes",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "row_custom_id",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "slug" => "sub_content_module",
                                "hasChildren" => 1,
                                "attributes" => [
                                    [
                                        "title" => "Tagline",
                                        "slug" => "tagline",
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
                                        "title" => "Tagline Heading Tag",
                                        "slug" => "tagline_heading_tag",
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
                                        "title" => "Title",
                                        "slug" => "content_module_title",
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
                                        "title" => "Title Heading Tag",
                                        "slug" => "title_heading_tag",
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
                                        "slug" => "content_module_description",
                                        "hasChildren" => 0,
                                        "type" => "editor",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "",
                                        "conditions" => [],
                                        "rules" => "",
                                        "is_required" => 0
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "title" => "Products",
                        "slug" => "products_module",
                        "column" => "12",
                        "groups" => [
                            [
                                "title" => "Products",
                                "slug" => "sub_products_module",
                                "hasChildren" => 1,
                                "attributes" => [
                                    [
                                        "title" => "Products",
                                        "slug" => "products",
                                        "hasChildren" => 1,
                                        "type" => "repeater",
                                        "conditions" => [],
                                        "description" => "",
                                        "rules" => "array",
                                        "is_required" => 0,
                                        "attributes" => [
                                            [
                                                [
                                                    "title" => "Product SKU",
                                                    "slug" => "product",
                                                    "hasChildren" => 0,
                                                    "type" => "text",
                                                    "provider" => "Modules\Product\Entities\Product",
                                                    "pluck" => ["name", "sku"],
                                                    "default" => "",
                                                    "options" => [],
                                                    "conditions" => [],
                                                    "description" => "",
                                                    "rules" => "exists:products,sku",
                                                    "is_required" => 1
                                                ],
                                                [
                                                    "title" => "Image",
                                                    "slug" => "image",
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
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "title" => "Items per row",
                                        "slug" => "feature_items_per_row",
                                        "hasChildren" => 0,
                                        "type" => "select",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "2",
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
                                    [
                                        "title" => "Add to Cart Button",
                                        "slug" => "add_to_cart_button",
                                        "hasChildren" => 0,
                                        "type" => "select",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "0",
                                        "options" => [
                                            [ "value" => 0, "label" => "Show" ],
                                            [ "value" => 1, "label" => "Hide" ]
                                        ],
                                        "conditions" => [],
                                        "description" => "",
                                        "rules" => "in:0,1",
                                        "is_required" => 1
                                    ],
                                    [
                                        "title" => "View More Button",
                                        "slug" => "view_more_button",
                                        "hasChildren" => 0,
                                        "type" => "select",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "0",
                                        "options" => [
                                            [ "value" => 0, "label" => "Enabled" ],
                                            [ "value" => 1, "label" => "Disable" ]
                                        ],
                                        "conditions" => [],
                                        "description" => "",
                                        "rules" => "in:0,1",
                                        "is_required" => 1
                                    ],
                                    [
                                        "title" => "View More Button Label",
                                        "slug" => "view_more_button_label",
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
                                        "title" => "View More Link",
                                        "slug" => "view_more_link",
                                        "hasChildren" => 0,
                                        "type" => "text",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "conditions" => [],
                                        "description" => "",
                                        "rules" => "url",
                                        "is_required" => 1
                                    ],
                                    [
                                        "title" => "Dynamic Link",
                                        "slug" => "dynamic_link",
                                        "hasChildren" => 0,
                                        "type" => "select",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "1",
                                        "options" => [
                                            [ "value" => 1, "label" => "Yes" ],
                                            [ "value" => 0, "label" => "No" ]
                                        ],
                                        "conditions" => [],
                                        "description" => "",
                                        "rules" => "in:0,1",
                                        "is_required" => 1
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        "title" => "Content with Image #1",
        "slug" => "content_with_image_one",
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
                                "slug" => "admin_title",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Background",
                        "slug" => "section_background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "section_background_color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "section_background_image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "section_background_position",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "section_background_size",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Video Link",
                                "slug" => "section_background_video_link",
                                "hasChildren" => 0,
                                "type" => "link",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                                "slug" => "padding_top",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Padding Bottom",
                                "slug" => "padding_bottom",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Padding Left/Right",
                                "slug" => "padding_left_right",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "section_advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "section_custom_classes",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "section_custom_id",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                        "slug" => "row_background",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Background Color",
                                "slug" => "row_background_color",
                                "hasChildren" => 0,
                                "type" => "color_picker",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Image",
                                "slug" => "row_background_image",
                                "hasChildren" => 0,
                                "type" => "file",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "mimes:jpeg,jpg,bmp,png",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Position",
                                "slug" => "row_background_position",
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
                                "is_required" => 0
                            ],
                            [
                                "title" => "Background Size",
                                "slug" => "row_background_size",
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
                                "is_required" => 0
                            ]
                        ]
                    ],
                    [
                        "title" => "Advanced",
                        "slug" => "row_advanced",
                        "hasChildren" => 1,
                        "attributes" => [
                            [
                                "title" => "Custom Classes",
                                "slug" => "row_custom_classes",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
                            ],
                            [
                                "title" => "Custom ID",
                                "slug" => "row_custom_id",
                                "hasChildren" => 0,
                                "type" => "text",
                                "provider" => "",
                                "pluck" => [],
                                "default" => "",
                                "options" => [],
                                "description" => "",
                                "conditions" => [],
                                "rules" => "",
                                "is_required" => 0
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
                        "title" => "Image",
                        "slug" => "image_module",
                        "column" => "7",
                        "groups" => [
                            [
                                "title" => "Image",
                                "slug" => "image_module_section",
                                "hasChildren" => 1,
                                "attributes" => [

                                    [
                                        "title" => "Image",
                                        "slug" => "image_module_image",
                                        "hasChildren" => 0,
                                        "type" => "file",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "",
                                        "conditions" => [],
                                        "rules" => "mimes:jpeg,jpg,bmp,png",
                                        "is_required" => 0
                                    ],
                                ]
                            ]
                        ]
                    ],
                    [
                        "title" => "Content",
                        "slug" => "content_module",
                        "column" => "5",
                        "groups" => [
                            [
                                "title" => "Content",
                                "slug" => "sub_content_module",
                                "hasChildren" => 1,
                                "attributes" => [

                                    [
                                        "title" => "Title",
                                        "slug" => "content_module_title",
                                        "hasChildren" => 0,
                                        "type" => "text",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "",
                                        "conditions" => [],
                                        "rules" => "",
                                        "is_required" => 0
                                    ],
                                    [
                                        "title" => "Title Heading Tag",
                                        "slug" => "title_heading_tag",
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
                                        "description" => "",
                                        "conditions" => [],
                                        "rules" => "in:h1,h2,h3,h4,h5,h6",
                                        "is_required" => 0
                                    ],
                                    [
                                        "title" => "Description",
                                        "slug" => "content_module_description",
                                        "hasChildren" => 0,
                                        "type" => "editor",
                                        "provider" => "",
                                        "pluck" => [],
                                        "default" => "",
                                        "options" => [],
                                        "description" => "",
                                        "conditions" => [],
                                        "rules" => "",
                                        "is_required" => 0
                                    ]
                                ]
                            ]
                        ]
                    ],

                ]
            ]
        ]
    ]
];
