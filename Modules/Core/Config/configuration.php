<?php
return[
    "general" => [
        "title" => "General",
        "children" => [
            [
                "title" => "General",
                "subChildren" => [
                    [
                        "title" => "Country Options",
                        "elements" => [
                            [
                                "title" => "Default Country",
                                "type" => "select",
                                "path" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "value" => "",
                                "values" => ["Male", "Female"],
                                "rules" => 'required|min:2|max:50',
                                "showIn" => ['default', 'store'],
                            ],
                            [
                                "title" => "Allow Countries",
                                "scope" => "Store",
                                "type" => "select",
                                "path" => "Modules\Core\Entities\Website",
                                "pluck" => ["code", "id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => "required|min:2|max:50",
                                "showIn" => ['channel', 'website', 'default'],
                            ]
                        ]
                    ],
                    [
                        "title" => "State Options",
                        "elements" => [
                            [
                                "title" => "Default Country",
                                "type" => "select",
                                "path" => "Modules\User\Entities\Admin",
                                "pluck" => ["first_name","id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => 'required|min:2|max:50',
                                "showIn" => ['default', 'website'],
                            ],
                            [
                                "title" => "Allow Countries",
                                "scope" => "Store",
                                "type" => "select",
                                "path" => "Modules\User\Entities\Role",
                                "pluck" => ["name","id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => "required|numeric",
                                "showIn" => ['channel', 'store', 'default'],
                            ]
                        ]
                    ]
                ]
            ],
            [
                "title" => "Web",
                "subChildren" => []
            ]
        ]
    ],
    "catalog" => [
        "title" => "Catalog",
        "children" => [
            [
                "title" => "Category",
                "subChildren" => [
                    [
                        "title" => "Country Options",
                        "elements" => [
                            [
                                "title" => "Default Country",
                                "type" => "select",
                                "path" => "Modules\Core\Entities\Channel",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "value" => ["code", "id"],
                                "values" => "",
                                "rules" => 'required|min:2|max:50',
                                "showIn" => ['default', 'store'],
                            ],
                            [
                                "title" => "Allow Countries",
                                "scope" => "Store",
                                "type" => "select",
                                "path" => "Modules\Category\Entities\Category",
                                "pluck" => ["name"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => "required|min:2|max:50",
                                "showIn" => ['channel', 'website', 'default'],
                            ]
                        ]
                    ],
                    [
                        "title" => "State Options",
                        "elements" => [
                            [
                                "title" => "Default Country",
                                "type" => "select",
                                "path" => "Modules\Core\Entities\Store",
                                "pluck" => ["name","id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => 'required|min:2|max:50',
                                "showIn" => ['default', 'website'],
                            ],
                            [
                                "title" => "Allow Countries",
                                "scope" => "Store",
                                "type" => "select",
                                "path" => "Modules\User\Entities\Role",
                                "pluck" => ["name","id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => "required|numeric",
                                "showIn" => ['channel', 'store', 'default'],
                            ]
                        ]
                    ]
                ]
            ],
            [
                "title" => "Web",
                "subChildren" => []
            ]
        ]
    ]
];
?>