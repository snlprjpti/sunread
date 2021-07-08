<?php

return [
    'model_list' => [
        'Channel' => 'Modules\Core\Entities\Channel',
        'Store' => 'Modules\Core\Entities\Store',
    ],

    'model_config' =>[
        'Store' => [
            'scope' => 'Modules\Core\Entities\Store',
            'parent' => 'channel',
            'parent_scope' => 'Modules\Core\Entities\Channel',
            ],
        'Channel' => [
            'scope' => 'Modules\Core\Entities\Channel',
            'parent' => 'website',
            'parent_scope' => 'Modules\Core\Entities\Website',
        ],
        'Website' => [
            'scope' => 'Modules\Core\Entities\Website',
            'parent' => null,
            'parent_scope' => null,
        ]
    ]
];
