<?php 
return [
    "properties" =>  [
        "id" =>  [
            "type" => "long"
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
        "type" =>  [
            "type" => "text",
            "fields" =>  [
                "keyword" =>  [
                    "type" => "keyword",
                    "ignore_above" => 256
                ]
            ]
        ],
        "attribute_group_id" =>  [
            "type" => "long"
        ],
        "channels" => [
            "properties"=> [
                "id"=> [
                    "type"=> "long"
                ],
            ]
        ],
        "categories" => [
            "properties"=> [
                "id"=> [
                    "type"=> "long"
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
                "slug"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "image"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "_lft"=> [
                    "type"=> "long"
                ],
                "_rgt"=> [
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
                "meta_description"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "meta_keywords"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "meta_title"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
                "position"=> [
                    "type"=> "long"
                ],
                "created_at"=> [
                    "type"=> "text",
                    "fields"=> [
                        "keyword"=> [
                            "type"=> "keyword",
                            "ignore_above"=> 256
                        ]
                    ]
                ],
            ]
        ],
        "product_attributes" =>  [
            "dynamic" => true, 
            "type" => "nested",
            "properties" =>  [
                "global" =>  [
                    "type" => "nested",
                    "dynamic" =>  true,
                    "properties"=> [
                        "attribute"=> [
                            "properties"=> [
                                "attribute_group_id"=> [
                                    "type"=> "long"
                                ],
                                "created_at"=> [
                                    "type"=> "date"
                                ],
                                "id"=> [
                                    "type"=> "long"
                                ],
                                "is_filterable"=> [
                                    "type"=> "long"
                                ],
                                "is_required"=> [
                                    "type"=> "long"
                                ],
                                "is_searchable"=> [
                                    "type"=> "long"
                                ],
                                "is_unique"=> [
                                    "type"=> "long"
                                ],
                                "is_user_defined"=> [
                                    "type"=> "long"
                                ],
                                "is_visible_on_front"=> [
                                    "type"=> "long"
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
                                "type"=> [
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
                                "validation"=> [
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
                        "value"=> [
                            "type"=> "integer",
                            "ignore_malformed"=> true
                        ],
                        "string_value" =>  [
                            "type" => "text",
                            "fields" =>  [
                                "keyword" =>  [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "boolean_value" =>  [
                            "type" => "long"
                        ],
                        "date_value" =>  [
                            "type" => "date"
                        ],
                        "integer_value" =>  [
                            "type" => "date"
                        ],
                        "decimal_value" =>  [
                            "type" => "double"
                        ],

                    ]
                ],
                "channel" =>  [
                    "type" => "nested",
                    "dynamic" =>  true,
                ],
                "store" =>  [
                    "type" => "nested",
                    "dynamic" =>  true,
                ],
            ]
        ],
        "status" =>  [
            "type" => "long"
        ],
        "created_at" =>  [
            "type" => "date"
        ],
        "updated_at" =>  [
            "type" => "date"
        ]
    ],
    "dynamic_templates"=> [
        [
          "channel_nested"=> [
            "path_match"=> "product_attributes.channel.*",
            "mapping"=> [
              "properties"=> [
                "attribute"=> [
                    "properties"=> [
                        "attribute_group_id"=> [
                            "type"=> "long"
                        ],
                        "created_at"=> [
                            "type"=> "date"
                        ],
                        "id"=> [
                            "type"=> "long"
                        ],
                        "is_filterable"=> [
                            "type"=> "long"
                        ],
                        "is_required"=> [
                            "type"=> "long"
                        ],
                        "is_searchable"=> [
                            "type"=> "long"
                        ],
                        "is_unique"=> [
                            "type"=> "long"
                        ],
                        "is_user_defined"=> [
                            "type"=> "long"
                        ],
                        "is_visible_on_front"=> [
                            "type"=> "long"
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
                        "type"=> [
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
                        "validation"=> [
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
                "value"=> [
                    "type"=> "integer",
                    "ignore_malformed"=> true
                ],
                "string_value" =>  [
                    "type" => "text",
                    "fields" =>  [
                        "keyword" =>  [
                            "type" => "keyword",
                            "ignore_above" => 256
                        ]
                    ]
                ],
                "boolean_value" =>  [
                    "type" => "long"
                ],
                "date_value" =>  [
                    "type" => "date"
                ],
                "integer_value" =>  [
                    "type" => "date"
                ],
                "decimal_value" =>  [
                    "type" => "double"
                ],
              ]
            ]
          ]
        ],
        [
            "store_nested"=> [
              "path_match"=> "product_attributes.store.*",
              "mapping"=> [
                "properties"=> [
                  "attribute"=> [
                      "properties"=> [
                          "attribute_group_id"=> [
                              "type"=> "long"
                          ],
                          "created_at"=> [
                              "type"=> "date"
                          ],
                          "id"=> [
                              "type"=> "long"
                          ],
                          "is_filterable"=> [
                              "type"=> "long"
                          ],
                          "is_required"=> [
                              "type"=> "long"
                          ],
                          "is_searchable"=> [
                              "type"=> "long"
                          ],
                          "is_unique"=> [
                              "type"=> "long"
                          ],
                          "is_user_defined"=> [
                              "type"=> "long"
                          ],
                          "is_visible_on_front"=> [
                              "type"=> "long"
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
                          "type"=> [
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
                          "validation"=> [
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
                  "value"=> [
                      "type"=> "integer",
                      "ignore_malformed"=> true
                  ],
                  "string_value" =>  [
                    "type" => "text",
                    "fields" =>  [
                        "keyword" =>  [
                            "type" => "keyword",
                            "ignore_above" => 256
                        ]
                    ]
                ],
                "boolean_value" =>  [
                    "type" => "long"
                ],
                "date_value" =>  [
                    "type" => "date"
                ],
                "integer_value" =>  [
                    "type" => "date"
                ],
                "decimal_value" =>  [
                    "type" => "double"
                ],
                ]
              ]
            ]
          ],
    ],

];