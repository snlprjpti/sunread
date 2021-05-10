<?php

use Modules\UrlRewrite\Entities\UrlRewrite;
use Modules\UrlRewrite\Repositories\UrlRewriteRepository;

return [
    'table-name' => 'url_rewrites',
    'repository' => UrlRewriteRepository::class,
    'model' => UrlRewrite::class,
    'cache' => false,
    'cache-tag' => 'url_rewrites',
    'cache-ttl' => 86400,
    'cache-decorator' => CachingUrlRewriteRepository::class,
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

