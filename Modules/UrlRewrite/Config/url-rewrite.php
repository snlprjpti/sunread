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
        'Category' => 'Modules\Category\Entities\Category',
        'Product' => 'Modules\Product\Entities\Product'
    ]
];

