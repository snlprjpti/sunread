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



    [
        'key'   => 'catalog.attributes.index',
        'name'  => 'List Attributes',
        'route' => 'admin.catalog.attributes.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'catalog.attributes.store',
        'name'  => 'Create Attributes',
        'route' => 'admin.catalog.attributes.store',
        'sort'  => 2,
    ],
    [
        'key'   => 'catalog.attributes.show',
        'name'  => 'Show Attributes',
        'route' => 'admin.catalog.attributes.show',
        'sort'  => 2,
    ],

    [
        'key'   => 'catalog.attributes.update',
        'name'  => 'Update Attributes',
        'route' => 'admin.catalog.attributes.update',
        'sort'  => 4,
    ],
    [
        'key'   => 'catalog.attributes.delete',
        'name'  => 'Delete Attributes',
        'route' => 'admin.catalog.attributes.delete',
        'sort'  => 5,
    ],

    //ATTRIBUTE FAMILIES
    [
        'key'   => 'catalog.families.index',
        'name'  => 'List Attribute Family',
        'route' => 'admin.catalog.families.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'catalog.families.store',
        'name'  => 'Create Attribute Family',
        'route' => 'admin.catalog.families.store',
        'sort'  => 2,
    ],

    [
        'key'   => 'catalog.families.show',
        'name'  => 'Show Attribute Family',
        'route' => 'admin.catalog.families.show',
        'sort'  => 3,
    ],
    [
        'key'   => 'catalog.families.update',
        'name'  => 'Update Attribute Family',
        'route' => 'admin.catalog.families.update',
        'sort'  => 4,
    ],
    [
        'key'   => 'catalog.families.delete',
        'name'  => 'Delete Attribute Family',
        'route' => 'admin.catalog.families.delete',
        'sort'  => 5,
    ],

    //ATTRIBUTE GROUPS
    [
        'key'   => 'catalog.attribute-groups.index',
        'name'  => 'List Attribute Group',
        'route' => 'admin.catalog.attribute-groups.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'catalog.attribute-groups.store',
        'name'  => 'Create Attribute Group',
        'route' => 'admin.catalog.attribute-groups.create',
        'sort'  => 2,
    ],

    [
        'key'   => 'catalog.attribute-groups.show',
        'name'  => 'Show Attribute Group',
        'route' => 'admin.catalog.attribute-groups.show',
        'sort'  => 3,
    ],
    [
        'key'   => 'catalog.attribute-groups.update',
        'name'  => 'Update Attribute Group',
        'route' => 'admin.catalog.attribute-groups.update',
        'sort'  => 4,
    ],
    [
        'key'   => 'catalog.attribute-groups.delete',
        'name'  => 'Delete Attribute Group',
        'route' => 'admin.catalog.attribute-groups.delete',
        'sort'  => 5,
    ],

    //CUSTOMERS
    [
        'key'   => 'customers.customers.list',
        'name'  => 'List customers',
        'route' => 'admin.customers.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'customers.customers.store',
        'name'  => 'Create customers',
        'route' => 'admin.customers.store',
        'sort'  => 2,
    ],
    [
        'key'   => 'customer.customers.show',
        'name'  => 'Show Customer Detail',
        'route' => 'admin.customers.show',
        'sort'  => 3,
    ],
    [
        'key'   => 'customer.customers.update',
        'name'  => 'Update Customer Detail',
        'route' => 'admin.customers.update',
        'sort'  => 4,
    ],
    [
        'key'   => 'customer.customers.delete',
        'name'  => 'Delete Customer Detail',
        'route' => 'admin.customers.delete',
        'sort'  => 5,
    ],

    //CUSTOMER-GROUP
    [
        'key'   => 'customers.groups.list',
        'name'  => 'List customers groups',
        'route' => 'admin.groups.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'customers.customers.store',
        'name'  => 'Create customer group',
        'route' => 'admin.groups.store',
        'sort'  => 2,
    ],
    [
        'key'   => 'customers.customers.show',
        'name'  => 'Show Customer Group',
        'route' => 'admin.groups.show',
        'sort'  => 3,
    ],
    [
        'key'   => 'customers.customers.update',
        'name'  => 'Update Customer Group',
        'route' => 'admin.groups.update',
        'sort'  => 4,
    ],
    [
        'key'   => 'customers.customers.delete',
        'name'  => 'Delete Customer Group',
        'route' => 'admin.groups.delete',
        'sort'  => 5,
    ],

    //CUSTOMER-ADDRESS
    [
        'key'   => 'customers.address.list',
        'name'  => 'List customers address',
        'route' => 'admin.customer.address.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'customers.address.store',
        'name'  => 'Create customer address',
        'route' => 'admin.customer.address.store',
        'sort'  => 2,
    ],
    [
        'key'   => 'customers.address.show',
        'name'  => 'Show Customer address',
        'route' => 'admin.customer.address.show',
        'sort'  => 3,
    ],
    [
        'key'   => 'admin.customers.update',
        'name'  => 'Update Customer address',
        'route' => 'admin.customer.address.update',
        'sort'  => 4,
    ],
    [
        'key'   => 'admin.customers.delete',
        'name'  => 'Delete Customer address',
        'route' => 'admin.customer.address.delete',
        'sort'  => 5,
    ],

];

