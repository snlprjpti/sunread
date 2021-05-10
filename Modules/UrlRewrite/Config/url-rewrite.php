<?php

use Modules\UrlRewrite\Entities\UrlRewrite;
use Modules\UrlRewrite\Repositories\UrlRewriteRepository;

return [
    'table-name' => 'url_rewrites',
    'repository' => UrlRewriteRepository::class,
    'model' => UrlRewrite::class,
    'cache' => true,
    'types' => [
        'product' => [
            'route' => 'product',
            'attributes' => ['id']
        ],
        'category' => [
            'route' => 'category',
            'attributes' => ['id']
        ],
    ],
];
