<?php

return [
    'table-name' => 'url_rewrites',
    'repository' => Modules\UrlRewrite\Repositories\UrlRewriteRepository::class,
    'model' => Modules\UrlRewrite\Entities\UrlRewrite::class,
    'cache' => false,
    'cache-tag' => 'url_rewrites',
    'cache-ttl' => 86400,
    'cache-decorator' => CachingUrlRewriteRepository::class,
    'path' => [
        'Category' => 'Modules\Category\Entities\Category',
        'Product' => 'Modules\Product\Entities\ProductAttribute'
    ]
];

