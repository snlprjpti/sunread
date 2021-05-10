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
            'route' => 'admin.catalog.products.show',
            'attributes' => ['id']
        ],
        'category' => [
            'route' => 'admin.catalog.categories.categories.show',
            'attributes' => ['id']
        ],
    ],
];

