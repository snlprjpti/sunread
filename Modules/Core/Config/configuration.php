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
                                "path" => "default_country",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => 'required',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                            ],
                            [
                                "title" => "Allow Countries",
                                "path" => "allow_countries",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code", "id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => "required",
                                "showIn" => ['channel', 'website', 'default', 'store'],
                            ],
                            [
                                "title" => "Zip/Postal Code is Optional for",
                                "path" => "optional_zip_countries",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code", "id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => "nullable",
                                "showIn" => ['channel', 'website', 'default', 'store'],
                            ]
                        ]
                    ],
                    [
                        "title" => "State Options",
                        "elements" => [
                            [
                                "title" => "State Country",
                                "path" => "state_country",
                                "type" => "select",
                                "provider" => "Modules\Core\Entities\Currency",
                                "pluck" => ["code","id"],
                                "default" => "",
                                "value" => "",
                                "values" => "",
                                "rules" => 'required',
                                "showIn" => ['channel', 'website', 'default', 'store'],
                            ],
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
        "children" => []
    ]
];
?>