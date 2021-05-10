<?php

use Modules\UrlRewrite\Entities\UrlRewrite;
use Modules\UrlRewrite\Repositories\UrlRewriteRepository;

return [
    'table-name' => 'url_rewrites',
    'repository' => UrlRewriteRepository::class,
    'model' => UrlRewrite::class,
    'cache' => true,
    'cache-tag' => 'url_rewrites',
    'cache-ttl' => 86400,
    'types' => [
        'product' => [
            'route' => 'product',
            'attributes' => ['id', 'store_id']
        ],
        'category' => [
            'route' => 'category',
            'attributes' => ['id', 'store_id']
        ],
    ],
];

