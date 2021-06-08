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
                "website_id"=> [
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
                "code"=> [
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
                "created_at"=> [
                    "type"=> "date"
                ],
                "updated_at"=> [
                    "type"=> "date"
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
                        ]
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
                ]
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
                  ]
                ]
              ]
            ]
          ],
    ],

];