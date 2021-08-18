<?php

return [
    "client" => [
        "hosts" => [
            env("ELASTIC_HOST", "localhost:9200"),
        ],
    ],
    "prefix" => env("ELASTIC_PREFIX", "sail_racing_store_")
];
