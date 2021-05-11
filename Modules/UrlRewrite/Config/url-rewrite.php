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
            'attributes' => [
                'parameter' => [ 'id' ],
                'extra_fields' => [ 'store_id' ],
                'parameter_key' => [ 'product' ]
            ]
        ],
        'category' => [
            'route' => 'admin.catalog.categories.categories.show',
            'attributes' => [
                'parameter' => [ 'id' ],
                'extra_fields' => [ 'store_id' ],
                'parameter_key' => [ 'category' ]
            ]
        ],
        'category_translation' => [
            'route' => 'admin.catalog.categories.categories.show',
            'attributes' => [
                'parameter' => [ 'category_id' ],
                'extra_fields' => [ 'store_id' ],
                'parameter_key' => [ 'category' ]
            ]
        ]
    ]
];

