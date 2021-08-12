<?php 
return [
    "properties" =>  [
        "id" =>  [
            "type" => "long"
        ],
        "name" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "sku" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "price" =>  [
            "type" => "float"
        ],
        "type" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "status" =>  [
            "type" => "long"
        ],
        "quantity"=> [
            "type"=> "text",
            "fields"=> [
                "keyword"=> [
                    "type"=> "keyword",
                    "ignore_above"=> 256
                ]
            ]
        ],
        "website_id" =>  [
            "type" => "long"
        ],
        "parent_id" =>  [
            "type" => "long"
        ],
        "visibility" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "visibility_value" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "is_in_stock"=> [
            "type"=> "long"
        ],
        "base_image" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "has_weight" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "has_weight_value" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "new_from_date" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "new_to_date" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "description" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "short_description" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "meta_keywords" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "meta_title" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "meta_description" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],        
        "special_price" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "special_from_date" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "special_to_date" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "tax_class_id" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "tax_class_id_value" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "url_key" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "weight" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "small_image" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "thumbnail_image" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "section_background" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "gallery" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "categories"=> [
            "properties"=> [
                "_lft"=> [
                    "type"=> "long"
                ],
                "_rgt"=> [
                    "type"=> "long"
                ],
                "created_at"=> [
                    "type"=> "date"
                ],
                "id"=> [
                    "type"=> "long"
                ],
                "position"=> [
                    "type"=> "long"
                ],
                "updated_at"=> [
                    "type"=> "date"
                ],
                "values"=> [
                    "properties"=> [
                        "attribute"=> [
                            "type"=> "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=> "keyword",
                                    "ignore_above"=> 256
                                ]
                            ]
                        ],
                        "category_id"=> [
                            "type"=> "long"
                        ],
                        "created_at"=> [
                            "type"=> "date"
                        ],
                        "id"=> [
                            "type"=> "long"
                        ],
                        "scope"=> [
                            "type"=> "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=> "keyword",
                                    "ignore_above"=> 256
                                ]
                            ]
                        ],
                        "scope_id"=> [
                            "type"=> "long"
                        ],
                        "updated_at"=> [
                            "type"=> "date"
                        ],
                        "value"=> [
                            "type"=> "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=> "keyword",
                                    "ignore_above"=> 256
                                ]
                            ]
                        ]
                    ]
                ],
                "website_id"=> [
                    "type"=> "long"
                ]
            ]
        ]
    ],

];
