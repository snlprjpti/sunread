<?php 
return [
    "properties" =>  [
        "attribute_group_id" =>  [
            "type" => "long"
        ],
        "created_at" =>  [
            "type" => "date"
        ],
        "categories"=> [
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
        "id" =>  [
            "type" => "long"
        ],
        "product_attributes" =>  [
            "type" => "nested",
            "properties" =>  [
                "global" =>  [
                    "type" => "object"
                ],
                "channel" =>  [
                    "type" => "object"
                ],
                "store" =>  [
                    "type" => "object"
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