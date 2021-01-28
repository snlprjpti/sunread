<?php

return [
    [
        'key'   => 'dashboard',
        'name'  => 'Dashboard',
        'route' => 'admin.dashboard.index',
        'sort'  => 1,
    ],

    [
        'key'   => 'sales',
        'name'  => 'sales',
        'route' => 'admin.sales.orders.index',
        'sort'  => 2,
    ],
    [
        'key'   => 'sales.orders',
        'name'  => 'orders',
        'route' => 'admin.sales.orders.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'sales.invoices',
        'name'  => 'invoices',
        'route' => 'admin.sales.invoices.index',
        'sort'  => 2,
    ],
    [
        'key'   => 'sales.shipments',
        'name'  => 'shipments',
        'route' => 'admin.sales.shipments.index',
        'sort'  => 3,
    ],
    [
        'key'   => 'catalog',
        'name'  => 'catalog',
        'route' => 'admin.catalog.index',
        'sort'  => 3,
    ],
    [
        'key'   => 'catalog.products',
        'name'  => 'products',
        'route' => 'admin.catalog.products.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'catalog.products.create',
        'name'  => 'create',
        'route' => 'admin.catalog.products.create',
        'sort'  => 1,
    ],
    [
        'key'   => 'catalog.products.edit',
        'name'  => 'edit',
        'route' => 'admin.catalog.products.edit',
        'sort'  => 2,
    ],
    [
        'key'   => 'catalog.products.delete',
        'name'  => 'delete',
        'route' => 'admin.catalog.products.delete',
        'sort'  => 3,
    ],
    [
        'key'   => 'catalog.categories',
        'name'  => 'categories',
        'route' => 'admin.catalog.categories.index',
        'sort'  => 2,
    ],
    [
        'key'   => 'catalog.categories.create',
        'name'  => 'create',
        'route' => 'admin.catalog.categories.create',
        'sort'  => 1,
    ], [
        'key'   => 'catalog.categories.edit',
        'name'  => 'edit',
        'route' => 'admin.catalog.categories.edit',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.categories.delete',
        'name'  => 'delete',
        'route' => 'admin.catalog.categories.delete',
        'sort'  => 3,
    ], [
        'key'   => 'catalog.attributes',
        'name'  => 'attributes',
        'route' => 'admin.catalog.attributes.index',
        'sort'  => 3,
    ], [
        'key'   => 'catalog.attributes.create',
        'name'  => 'create',
        'route' => 'admin.catalog.attributes.create',
        'sort'  => 1,
    ], [
        'key'   => 'catalog.attributes.edit',
        'name'  => 'edit',
        'route' => 'admin.catalog.attributes.edit',
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
        'key'   => 'catalog.families.edit',
        'name'  => 'edit',
        'route' => 'admin.catalog.families.edit',
        'sort'  => 2,
    ], [
        'key'   => 'catalog.families.delete',
        'name'  => 'delete',
        'route' => 'admin.catalog.families.delete',
        'sort'  => 3,
    ], [
        'key'   => 'customers',
        'name'  => 'customers',
        'route' => 'admin.customer.index',
        'sort'  => 4,
    ], [
        'key'   => 'customers.customers',
        'name'  => 'customers',
        'route' => 'admin.customer.index',
        'sort'  => 1,
    ], [
        'key'   => 'customers.customers.create',
        'name'  => 'create',
        'route' => 'admin.customer.create',
        'sort'  => 1,
    ], [
        'key'   => 'customers.customers.edit',
        'name'  => 'edit',
        'route' => 'admin.customer.edit',
        'sort'  => 2,
    ], [
        'key'   => 'customers.customers.delete',
        'name'  => 'delete',
        'route' => 'admin.customer.delete',
        'sort'  => 3,
    ], [
        'key'   => 'customers.groups',
        'name'  => 'groups',
        'route' => 'admin.groups.index',
        'sort'  => 2,
    ], [
        'key'   => 'customers.groups.create',
        'name'  => 'create',
        'route' => 'admin.groups.create',
        'sort'  => 1,
    ], [
        'key'   => 'customers.groups.edit',
        'name'  => 'edit',
        'route' => 'admin.groups.edit',
        'sort'  => 2,
    ], [
        'key'   => 'customers.groups.delete',
        'name'  => 'delete',
        'route' => 'admin.groups.delete',
        'sort'  => 3,
    ], [
        'key'   => 'customers.reviews',
        'name'  => 'reviews',
        'route' => 'admin.customer.review.index',
        'sort'  => 3,
    ], [
        'key'   => 'customers.reviews.edit',
        'name'  => 'edit',
        'route' => 'admin.customer.review.edit',
        'sort'  => 1,
    ], [
        'key'   => 'customers.reviews.delete',
        'name'  => 'delete',
        'route' => 'admin.customer.review.delete',
        'sort'  => 2,
    ], [
        'key'   => 'configuration',
        'name'  => 'configure',
        'route' => 'admin.configuration.index',
        'sort'  => 5,
    ], [
        'key'   => 'settings',
        'name'  => 'settings',
        'route' => 'admin.users.index',
        'sort'  => 6,
    ], [
        'key'   => 'settings.locales',
        'name'  => 'locales',
        'route' => 'admin.locales.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.locales.create',
        'name'  => 'create',
        'route' => 'admin.locales.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.locales.edit',
        'name'  => 'edit',
        'route' => 'admin.locales.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.locales.delete',
        'name'  => 'delete',
        'route' => 'admin.locales.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.currencies',
        'name'  => 'currencies',
        'route' => 'admin.currencies.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.currencies.create',
        'name'  => 'create',
        'route' => 'admin.currencies.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.currencies.edit',
        'name'  => 'edit',
        'route' => 'admin.currencies.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.currencies.delete',
        'name'  => 'delete',
        'route' => 'admin.currencies.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.exchange_rates',
        'name'  => 'exchange-rates',
        'route' => 'admin.exchange_rates.index',
        'sort'  => 3,
    ], [
        'key'   => 'settings.exchange_rates.create',
        'name'  => 'create',
        'route' => 'admin.exchange_rates.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.exchange_rates.edit',
        'name'  => 'edit',
        'route' => 'admin.exchange_rates.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.exchange_rates.delete',
        'name'  => 'delete',
        'route' => 'admin.exchange_rates.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.inventory_sources',
        'name'  => 'inventory-sources',
        'route' => 'admin.inventory_sources.index',
        'sort'  => 4,
    ], [
        'key'   => 'settings.inventory_sources.create',
        'name'  => 'create',
        'route' => 'admin.inventory_sources.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.inventory_sources.edit',
        'name'  => 'edit',
        'route' => 'admin.inventory_sources.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.inventory_sources.delete',
        'name'  => 'delete',
        'route' => 'admin.inventory_sources.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.channels',
        'name'  => 'channels',
        'route' => 'admin.channels.index',
        'sort'  => 5,
    ], [
        'key'   => 'settings.channels.create',
        'name'  => 'create',
        'route' => 'admin.channels.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.channels.edit',
        'name'  => 'edit',
        'route' => 'admin.channels.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.channels.delete',
        'name'  => 'delete',
        'route' => 'admin.channels.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.users',
        'name'  => 'users',
        'route' => 'admin.users.index',
        'sort'  => 6,
    ], [
        'key'   => 'settings.users.users',
        'name'  => 'users',
        'route' => 'admin.users.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.users.create',
        'name'  => 'create',
        'route' => 'admin.users.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.users.edit',
        'name'  => 'edit',
        'route' => 'admin.users.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.users.users.delete',
        'name'  => 'delete',
        'route' => 'admin.users.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.users.roles',
        'name'  => 'roles',
        'route' => 'admin.roles.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.users.roles.create',
        'name'  => 'create',
        'route' => 'admin.roles.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.roles.edit',
        'name'  => 'edit',
        'route' => 'admin.roles.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.users.roles.delete',
        'name'  => 'delete',
        'route' => 'admin.roles.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.sliders',
        'name'  => 'sliders',
        'route' => 'admin.sliders.index',
        'sort'  => 7,
    ], [
        'key'   => 'settings.sliders.create',
        'name'  => 'create',
        'route' => 'admin.sliders.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.sliders.edit',
        'name'  => 'edit',
        'route' => 'admin.sliders.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.sliders.delete',
        'name'  => 'delete',
        'route' => 'admin.sliders.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.taxes',
        'name'  => 'taxes',
        'route' => 'admin.tax-categories.index',
        'sort'  => 8,
    ], [
        'key'   => 'settings.taxes.tax-categories',
        'name'  => 'tax-categories',
        'route' => 'admin.tax-categories.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.taxes.tax-categories.create',
        'name'  => 'create',
        'route' => 'admin.tax-categories.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.taxes.tax-categories.edit',
        'name'  => 'edit',
        'route' => 'admin.tax-categories.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.taxes.tax-categories.delete',
        'name'  => 'delete',
        'route' => 'admin.tax-categories.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.taxes.tax-rates',
        'name'  => 'tax-rates',
        'route' => 'admin.tax-rates.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.taxes.tax-rates.create',
        'name'  => 'create',
        'route' => 'admin.tax-rates.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.taxes.tax-rates.edit',
        'name'  => 'edit',
        'route' => 'admin.tax-rates.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.taxes.tax-rates.delete',
        'name'  => 'delete',
        'route' => 'admin.tax-rates.delete',
        'sort'  => 3,
    ]
];

