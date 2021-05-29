<?php 
return [
    "properties" =>  [
        "attribute_group_id" =>  [
            "type" => "long"
        ],
        "created_at" =>  [
            "type" => "date"
        ],
        "categories" => [
            "properties"=> [
                "_lft"=> [
                    "type"=>  "long"
                ],
                "_rgt"=> [
                    "type"=>  "long"
                ],
                "created_at"=> [
                    "type"=>  "date"
                ],
                "id"=> [
                    "type"=>  "long"
                ],
                "name"=> [
                    "type"=>  "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=>  "keyword",
                            "ignore_above"=>  256
                        ]
                    ]
                ],
                "pivot"=> [
                    "properties"=> [
                        "category_id"=> [
                            "type"=>  "long"
                        ],
                        "product_id"=> [
                            "type"=>  "long"
                        ]
                    ]
                ],
                "position"=> [
                    "type"=>  "long"
                ],
                "slug"=> [
                    "type"=>  "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=>  "keyword",
                            "ignore_above"=>  256
                        ]
                    ]
                ],
                "status"=> [
                    "type"=>  "long"
                ],
                "translations"=> [
                    "properties"=> [
                        "category_id"=> [
                            "type"=>  "long"
                        ],
                        "id"=> [
                            "type"=>  "long"
                        ],
                        "name"=> [
                            "type"=>  "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=>  "keyword",
                                    "ignore_above"=>  256
                                ]
                            ]
                        ],
                        "store_id"=> [
                            "type"=>  "long"
                        ],
                        "url"=> [
                            "type"=>  "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=>  "keyword",
                                    "ignore_above"=>  256
                                ]
                            ]
                        ]
                    ]
                ],
                "updated_at"=> [
                    "type"=>  "date"
                ],
                "url"=> [
                    "type"=>  "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=>  "keyword",
                            "ignore_above"=>  256
                        ]
                    ]
                ]
            ]
        ],
        "channels" => [
            "properties"=> [
                "code"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                            ]
                        ]
                    ],
                "created_at"=> [
                    "type"=> "date"
                ],
                "default_category_id"=> [
                    "type"=> "long"
                ],
                "default_currency"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "default_store_id"=> [
                    "type"=> "long"
                ],
                "description"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "favicon"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "hostname"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],                    
                "id"=> [
                    "type"=> "long"
                ],
                "location"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "logo"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "name"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "pivot"=> [
                    "properties"=> [
                        "channel_id"=> [
                            "type"=> "long"
                        ],
                        "product_id"=> [
                            "type"=> "long"
                        ]
                    ]
                ],
                "stores"=> [
                    "properties"=> [
                        "created_at"=> [
                            "type"=> "date"
                        ],
                        "currency"=> [
                            "type"=> "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=> "keyword",
                                    "ignore_above"=> 256
                                ]
                            ]
                        ],
                        "id"=> [
                            "type"=> "long"
                        ],
                        "locale"=> [
                            "type"=> "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=> "keyword",
                                    "ignore_above"=> 256
                                ]
                            ]
                        ],
                        "name"=> [
                            "type"=> "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=> "keyword",
                                    "ignore_above"=> 256
                                ]
                            ]
                        ],
                        "pivot"=> [
                            "properties"=> [
                                "channel_id"=> [
                                    "type"=> "long"
                                ],
                                "store_id"=> [
                                    "type"=> "long"
                                ]
                            ]
                        ],
                        "position"=> [
                            "type"=> "long"
                        ],
                        "slug"=> [
                            "type"=> "text",
                            "fields"=> [
                                "keyword"=> [
                                    "type"=> "keyword",
                                    "ignore_above"=> 256
                                ]
                            ]
                        ],
                        "updated_at"=> [
                            "type"=> "date"
                        ]
                    ]
                ],
                "theme"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "timezone"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "updated_at"=> [
                    "type"=> "date"
                ],
                "website_id"=> [
                    "type"=> "long"
                ]
            ]
        ],
        "id" =>  [
            "type" => "long"
        ],
        "product_attributes" =>  [
            "type" => "nested",
            "properties" =>  [
                "global" =>  [
                    "type" => "nested",
                    "dynamic" =>  true,
                    "properties" => [
                        "price"=> [
                            "properties"=> [
                                "attribute_group"=> [
                                    "properties"=> [
                                        "attribute_family"=> [
                                            "properties"=> [
                                                "id"=> [
                                                    "type"=>  "long"
                                                ],
                                                "name"=> [
                                                    "type"=>  "text",
                                                    "fields"=> [
                                                        "keyword"=> [
                                                            "type"=>  "keyword",
                                                            "ignore_above"=>  256
                                                        ]
                                                    ]
                                                ],
                                                "slug"=> [
                                                    "type"=>  "text",
                                                    "fields"=> [
                                                        "keyword"=> [
                                                            "type"=>  "keyword",
                                                            "ignore_above"=>  256
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        "id"=> [
                                            "type"=>  "long"
                                        ],
                                        "name"=> [
                                            "type"=>  "text",
                                            "fields"=> [
                                                "keyword"=> [
                                                    "type"=>  "keyword",
                                                    "ignore_above"=>  256
                                                ]
                                            ]
                                        ],
                                        "slug"=> [
                                            "type"=>  "text",
                                            "fields"=> [
                                                "keyword"=> [
                                                    "type"=>  "keyword",
                                                    "ignore_above"=>  256
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "id"=> [
                                    "type"=>  "long"
                                ],
                                "name"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "slug"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "type"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "value"=> [
                                    "type"=>  "long"
                                ]
                            ]
                        ],
                                       
                    ]
                ],
                "channel" =>  [
                    "dynamic" =>  true,
                    "type" => "nested",
                    "properties" => [
                        "price"=> [
                            "properties"=> [
                                "attribute_group"=> [
                                    "properties"=> [
                                        "attribute_family"=> [
                                            "properties"=> [
                                                "id"=> [
                                                    "type"=>  "long"
                                                ],
                                                "name"=> [
                                                    "type"=>  "text",
                                                    "fields"=> [
                                                        "keyword"=> [
                                                            "type"=>  "keyword",
                                                            "ignore_above"=>  256
                                                        ]
                                                    ]
                                                ],
                                                "slug"=> [
                                                    "type"=>  "text",
                                                    "fields"=> [
                                                        "keyword"=> [
                                                            "type"=>  "keyword",
                                                            "ignore_above"=>  256
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        "id"=> [
                                            "type"=>  "long"
                                        ],
                                        "name"=> [
                                            "type"=>  "text",
                                            "fields"=> [
                                                "keyword"=> [
                                                    "type"=>  "keyword",
                                                    "ignore_above"=>  256
                                                ]
                                            ]
                                        ],
                                        "slug"=> [
                                            "type"=>  "text",
                                            "fields"=> [
                                                "keyword"=> [
                                                    "type"=>  "keyword",
                                                    "ignore_above"=>  256
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "id"=> [
                                    "type"=>  "long"
                                ],
                                "name"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "slug"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "type"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "value"=> [
                                    "type"=>  "long"
                                ]
                            ]
                        ],
                                       
                    ]
                ],
                "store" =>  [
                    "dynamic" =>  true,
                    "type" => "nested",
                    "properties" => [
                        "price"=> [
                            "properties"=> [
                                "attribute_group"=> [
                                    "properties"=> [
                                        "attribute_family"=> [
                                            "properties"=> [
                                                "id"=> [
                                                    "type"=>  "long"
                                                ],
                                                "name"=> [
                                                    "type"=>  "text",
                                                    "fields"=> [
                                                        "keyword"=> [
                                                            "type"=>  "keyword",
                                                            "ignore_above"=>  256
                                                        ]
                                                    ]
                                                ],
                                                "slug"=> [
                                                    "type"=>  "text",
                                                    "fields"=> [
                                                        "keyword"=> [
                                                            "type"=>  "keyword",
                                                            "ignore_above"=>  256
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        "id"=> [
                                            "type"=>  "long"
                                        ],
                                        "name"=> [
                                            "type"=>  "text",
                                            "fields"=> [
                                                "keyword"=> [
                                                    "type"=>  "keyword",
                                                    "ignore_above"=>  256
                                                ]
                                            ]
                                        ],
                                        "slug"=> [
                                            "type"=>  "text",
                                            "fields"=> [
                                                "keyword"=> [
                                                    "type"=>  "keyword",
                                                    "ignore_above"=>  256
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "id"=> [
                                    "type"=>  "long"
                                ],
                                "name"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "slug"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "type"=> [
                                    "type"=>  "text",
                                    "fields"=> [
                                        "keyword"=> [
                                            "type"=>  "keyword",
                                            "ignore_above"=>  256
                                        ]
                                    ]
                                ],
                                "value"=> [
                                    "type"=>  "long"
                                ]
                            ]
                        ],
                                       
                    ]
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
        "status" =>  [
            "type" => "long"
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
        "updated_at" =>  [
            "type" => "date"
        ]
    ]
];