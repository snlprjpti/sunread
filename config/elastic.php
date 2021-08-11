<?php

return [
    'client' => [
        'hosts' => [
            env('ELASTIC_HOST', 'localhost:9200'),
        ],
    ],
];
