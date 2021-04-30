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
                                "path" => "\Modules\Core\Entities\Currency",
                                "pluck" => "code","id",
                                "default" => "",
                                "value" => "",
                                "rules" => 'required|min:2|max:50',
                                "showIn" => ['default', 'store'],
                            ],
                            [
                                "title" => "Allow Countries",
                                "scope" => "Store",
                                "type" => "select",
                                "path" =>"\Modules\Category\Entities\Category",
                                "pluck" => "name","id",
                                "default" => "",
                                "value" => "",
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
                                "path" => "\Modules\User\Entities\Admin",
                                "pluck" => "first_name","id",
                                "default" => "",
                                "value" => "",
                                "rules" => 'required|min:2|max:50',
                                "showIn" => ['default', 'website'],
                            ],
                            [
                                "title" => "Allow Countries",
                                "scope" => "Store",
                                "type" => "select",
                                "path" =>"\Modules\User\Entities\Role",
                                "pluck" => "name","id",
                                "default" => "",
                                "value" => "",
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
    "catalog" => []
];
?>