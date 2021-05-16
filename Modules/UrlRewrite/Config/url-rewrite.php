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
    'path' => [
        'product' => 'Modules\Category\Entities\Category',
        'category' => 'Modules\Product\Entities\Product'
    ]
];

