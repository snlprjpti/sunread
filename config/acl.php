<?php

return [
    [
        'key'   => 'dashboard',
        'name'  => 'Dashboard',
        'route' => 'admin.dashboard.index',
        'sort'  => 1,
        "module"=>'Dashboard'
    ],

    //PRODUCT PERMISSION
    [
        'key'   => 'catalog.products.index',
        'name'  => "List Product ",
        'route' => 'admin.catalog.products.index',
        'sort'  => 1,
        'module' => 'Product'
    ],

    [
        'key'   => 'catalog.products.store',
        'name'  => 'Create Product',
        'route' => 'admin.catalog.products.store',
        'module' => 'Product'
    ],

    [
        'key'   => 'catalog.products.show',
        'name'  => 'Show Product',
        'route' => 'admin.catalog.products.show',
        'sort'  => 3,
        'module' => 'Product'
    ],
    [
        'key'   => 'catalog.products.update',
        'name'  => 'Update Product',
        'route' => 'admin.catalog.products.update',
        'sort'  => 4,
        'module' => 'Product'
    ],
    [
        'key'   => 'catalog.products.delete',
        'name'  => 'Delete Product',
        'route' => 'admin.catalog.products.delete',
        'sort'  => 5,
        'module' => 'Product'
    ],


    //CATEGORY PERMISSION

    [
        'key'   => 'catalog.categories.index',
        'name'  => 'List Categories',
        'route' => 'admin.catalog.categories.index',
        'sort'  => 1,
        'module' => 'Category'
    ],
    [
        'key'   => 'catalog.categories.store',
        'name'  => 'Create Category',
        'route' => 'admin.catalog.categories.store',
        'sort'  => 2,
        'module' => 'Category'
    ],
    [
        'key'   => 'catalog.categories.show',
        'name'  => 'Show Category',
        'route' => 'admin.catalog.categories.index',
        'sort'  => 3,
        'module' => 'Category'
    ],

    [
        'key'   => 'catalog.categories.update',
        'name'  => 'Update Category',
        'route' => 'admin.catalog.categories.update',
        'sort'  => 4,
        'module' => 'Category'
    ], [
        'key'   => 'catalog.categories.delete',
        'name'  => 'Delete Category',
        'route' => 'admin.catalog.categories.delete',
        'sort'  => 5,
        'module' => 'Category'
    ],



    /*[
        'key'   => 'catalog.attributes',
        'name'  => 'attributes',
        'route' => 'admin.catalog.attributes.index',
        'sort'  => 5,
    ],
    [
        'key'   => 'catalog.attributes.create',
        'name'  => 'create',
        'route' => 'admin.catalog.attributes.create',
        'sort'  => 1,
    ],
    [
        'key'   => 'catalog.attributes.update',
        'name'  => 'update',
        'route' => 'admin.catalog.attributes.update',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.attributes.delete',
        'name'  => 'delete',
        'route' => 'admin.catalog.attributes.delete',
        'sort'  => 3,
    ], [
        'key'   => 'catalog.families',
        'name'  => 'attribute-families',
        'route' => 'admin.catalog.families.index',
        'sort'  => 4,
    ], [
        'key'   => 'catalog.families.create',
        'name'  => 'create',
        'route' => 'admin.catalog.families.create',
        'sort'  => 1,
    ], [
        'key'   => 'catalog.families.update',
        'name'  => 'update',
        'route' => 'admin.catalog.families.update',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.families.delete',
        'name'  => 'delete',
        'route' => 'admin.catalog.families.delete',
        'sort'  => 3,
    ],
    [
        'key'   => 'customers',
        'name'  => 'customers',
        'route' => 'admin.customer.index',
        'sort'  => 4,
    ],
    [
        'key'   => 'customers.customers',
        'name'  => 'customers',
        'route' => 'admin.customer.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'customers.customers.create',
        'name'  => 'create',
        'route' => 'admin.customer.create',
        'sort'  => 1,
    ],
    [
        'key'   => 'customers.customers.update',
        'name'  => 'update',
        'route' => 'admin.customer.update',
        'sort'  => 2,
    ],
    [
        'key'   => 'customers.customers.delete',
        'name'  => 'delete',
        'route' => 'admin.customer.delete',
        'sort'  => 3,
    ],
    [
        'key'   => 'customers.groups',
        'name'  => 'groups',
        'route' => 'admin.groups.index',
        'sort'  => 2,
    ],
    [
        'key'   => 'customers.groups.create',
        'name'  => 'create',
        'route' => 'admin.groups.create',
        'sort'  => 1,
    ],
    [
        'key'   => 'customers.groups.update',
        'name'  => 'update',
        'route' => 'admin.groups.update',
        'sort'  => 2,
    ],
    [
        'key'   => 'customers.groups.delete',
        'name'  => 'delete',
        'route' => 'admin.groups.delete',
        'sort'  => 3,
    ]*/
];

