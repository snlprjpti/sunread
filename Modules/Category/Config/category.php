<?php 
return [
    "attributes" => [
        "general" => [
            "title" => "General Details",
            "elements" => [
                [
                    "title" => "Name",
                    "slug" => "name",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "rules" => "string",
                    "is_required" => 1
                ],
                [
                    "title" => "Status",
                    "slug" => "status",
                    "type" => "boolean",
                    "value" => "",
                    "scope" => "store",
                    "rules" => "nullable|boolean",
                    "is_required" => 0
                ],
                [
                    "title" => "Include In Menu",
                    "slug" => "include_in_menu",
                    "type" => "boolean",
                    "value" => "",
                    "scope" => "store",
                    "rules" => "nullable|boolean",
                    "is_required" => 0
                    
                ]
            ]
        ],
        "content" => [
            "title" => "Content",
            "elements" => [
                [
                    "title" => "Image",
                    "slug" => "image",
                    "type" => "file",
                    "value" => "",
                    "scope" => "store",
                    "rules" => "nullable|mimes:jpeg,jpg,bmp,png",
                    "is_required" => 0            
                ],
                [
                    "title" => "Description",
                    "slug" => "description",
                    "type" => "textarea",
                    "value" => "",
                    "scope" => "store",
                    "rules" => "nullable|string",
                    "is_required" => 0
                    
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
                    "title" => "Slug",
                    "slug" => "slug",
                    "type" => "text",
                    "value" => "",
                    "scope" => "store",
                    "rules" => "string",
                    "is_required" => 0
                    
                ],
                [
                    "title" => "Meta Title",
                    "slug" => "meta_title",
                    "type" => "text",
                    "value" => "",
                    "scope" => "website",
                    "rules" => "nullable",
                    "is_required" => 0
                    
                ],
                [
                    "title" => "Meta KeyWords", 
                    "slug" => "meta_keywords",
                    "type" => "text",
                    "value" => "",
                    "scope" => "channel",
                    "rules" => "nullable",
                    "is_required" => 0
                    
                ],
                [
                    "title" => "Meta Description",
                    "slug" => "meta_description",
                    "type" => "textarea",
                    "value" => "",
                    "scope" => "store",
                    "rules" => "nullable",
                    "is_required" => 0
                    
                ]
            ]
        ],
    ],
    "absolute_path" => [
        "name" => "general.elements.0",
        "status" => "general.elements.1",
        "include_in_menu" => "general.elements.2",
        "image" => "content.elements.0",
        "description" => "content.elements.1",
        "slug" => "search_engine_optimization.elements.0",
        "meta_title" => "search_engine_optimization.elements.1",
        "meta_keywords" => "search_engine_optimization.elements.2",
        "meta_description" => "search_engine_optimization.elements.3"
    ]
];