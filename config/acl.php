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
    ],
    [
        'key'   => 'catalog.categories.delete',
        'name'  => 'Delete Category',
        'route' => 'admin.catalog.categories.delete',
        'sort'  => 5,
        'module' => 'Category'
    ],

    
    //STORE
    [
        'key'   => 'core.stores.index',
        'name'  => 'List stores',
        'route' => 'admin.stores.index',
        'sort'  => 1,
        'module' => 'Core'
    ],
    [
        'key'   => 'core.stores.store',
        'name'  => 'Create stores',
        'route' => 'admin.stores.store',
        'sort'  => 2,
        'module' => 'Core'
    ],
    [
        'key'   => 'core.stores.show',
        'name'  => 'Show stores',
        'route' => 'admin.stores.show',
        'sort'  => 3,
        'module' => 'Core'
    ],
    [
        'key'   => 'core.stores.update',
        'name'  => 'Update stores',
        'route' => 'admin.stores.update',
        'sort'  => 4,
        'module' => 'Core'
    ],
    [
        'key'   => 'core.stores.delete',
        'name'  => 'Delete stores',
        'route' => 'admin.stores.delete',
        'sort'  => 5,
        'module' => 'Core'
    ],

    
    //WEBSITES
    [
        'key'   => 'core.websites.index',
        'name'  => 'List Website',
        'route' => 'core.websites.index',
        'sort'  => 1,
        "module" => 'Core'
    ],
    [
        'key'   => 'core.websites.store',
        'name'  => 'Create Website',
        'route' => 'core.websites.store',
        'sort'  => 2,
        "module" => 'Core'
    ],
    [
        'key'   => 'core.websites.show',
        'name'  => 'Show Website',
        'route' => 'core.websites.show',
        'sort'  => 3,
        "module" => 'Core'
    ],
    [
        'key'   => 'core.websites.update',
        'name'  => 'Update Website',
        'route' => 'core.websites.update',
        'sort'  => 4,
        "module" => 'Core'
    ],
    [
        'key'   => 'core.websites.delete',
        'name'  => 'Delete Website',
        'route' => 'core.websites.delete',
        'sort'  => 5,
        "module" => 'Core'
    ],


    //CONFIGURATION
    [
        'key'   => 'core.configurations.index',
        'name'  => 'List configurations',
        'route' => 'core.configurations.index',
        'sort'  => 1,
        "module" => 'Core'
    ],
    [
        'key'   => 'core.configurations.store',
        'name'  => 'Create configurations',
        'route' => 'core.configurations.store',
        'sort'  => 2,
        "module" => 'Core'
    ],
    [
        'key'   => 'core.configurations.show',
        'name'  => 'Show configurations',
        'route' => 'core.configurations.show',
        'sort'  => 3,
        "module" => 'Core'
    ],
    [
        'key'   => 'core.configurations.update',
        'name'  => 'Update configurations',
        'route' => 'core.configurations.update',
        'sort'  => 4,
        "module" => 'Core'
    ],
    [
        'key'   => 'core.configurations.delete',
        'name'  => 'Delete configurations',
        'route' => 'core.configurations.delete',
        'sort'  => 5,
        "module" => 'Core'
    ],


    //ATTRIBUTE FAMILIES
    [
        'key'   => 'catalog.families.index',
        'name'  => 'List Attribute Family',
        'route' => 'admin.catalog.families.index',
        'sort'  => 1,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.families.store',
        'name'  => 'Create Attribute Family',
        'route' => 'admin.catalog.families.store',
        'sort'  => 2,
        'module' => 'Attribute'
    ],

    [
        'key'   => 'catalog.families.show',
        'name'  => 'Show Attribute Family',
        'route' => 'admin.catalog.families.show',
        'sort'  => 3,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.families.update',
        'name'  => 'Update Attribute Family',
        'route' => 'admin.catalog.families.update',
        'sort'  => 4,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.families.delete',
        'name'  => 'Delete Attribute Family',
        'route' => 'admin.catalog.families.delete',
        'sort'  => 5,
        'module' => 'Attribute'
    ],

    //ATTRIBUTE GROUPS
    [
        'key'   => 'catalog.attribute-groups.index',
        'name'  => 'List Attribute Group',
        'route' => 'admin.catalog.attribute-groups.index',
        'sort'  => 1,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.attribute-groups.store',
        'name'  => 'Create Attribute Group',
        'route' => 'admin.catalog.attribute-groups.store',
        'sort'  => 2,
        'module' => 'Attribute'
    ],

    [
        'key'   => 'catalog.attribute-groups.show',
        'name'  => 'Show Attribute Group',
        'route' => 'admin.catalog.attribute-groups.show',
        'sort'  => 3,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.attribute-groups.update',
        'name'  => 'Update Attribute Group',
        'route' => 'admin.catalog.attribute-groups.update',
        'sort'  => 4,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.attribute-groups.delete',
        'name'  => 'Delete Attribute Group',
        'route' => 'admin.catalog.attribute-groups.delete',
        'sort'  => 5,
        'module' => 'Attribute'
    ],
    //Attribute
    [
        'key'   => 'catalog.attributes.index',
        'name'  => 'List Attributes',
        'route' => 'admin.catalog.attributes.index',
        'sort'  => 1,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.attributes.store',
        'name'  => 'Create Attributes',
        'route' => 'admin.catalog.attributes.store',
        'sort'  => 2,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.attributes.show',
        'name'  => 'Show Attributes',
        'route' => 'admin.catalog.attributes.show',
        'sort'  => 3,
        'module' => 'Attribute'
    ],

    [
        'key'   => 'catalog.attributes.update',
        'name'  => 'Update Attributes',
        'route' => 'admin.catalog.attributes.update',
        'sort'  => 4,
        'module' => 'Attribute'
    ],
    [
        'key'   => 'catalog.attributes.delete',
        'name'  => 'Delete Attributes',
        'route' => 'admin.catalog.attributes.delete',
        'sort'  => 5,
        'module' => 'Attribute'
    ],


    //CUSTOMERS
    [
        'key'   => 'customers.customers.list',
        'name'  => 'List customers',
        'route' => 'admin.customers.index',
        'sort'  => 1,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.customers.store',
        'name'  => 'Create customers',
        'route' => 'admin.customers.store',
        'sort'  => 2,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.customers.show',
        'name'  => 'Show Customer Detail',
        'route' => 'admin.customers.show',
        'sort'  => 3,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.customers.update',
        'name'  => 'Update Customer Detail',
        'route' => 'admin.customers.update',
        'sort'  => 4,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.customers.delete',
        'name'  => 'Delete Customer Detail',
        'route' => 'admin.customers.delete',
        'sort'  => 5,
        'module' => 'Customers'
    ],


    //CUSTOMER-GROUP
    [
        'key'   => 'customers.groups.list',
        'name'  => 'List customers groups',
        'route' => 'admin.groups.index',
        'sort'  => 1,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.customers.store',
        'name'  => 'Create customer group',
        'route' => 'admin.groups.store',
        'sort'  => 2,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.customers.show',
        'name'  => 'Show Customer Group',
        'route' => 'admin.groups.show',
        'sort'  => 3,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.customers.update',
        'name'  => 'Update Customer Group',
        'route' => 'admin.groups.update',
        'sort'  => 4,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.customers.delete',
        'name'  => 'Delete Customer Group',
        'route' => 'admin.groups.delete',
        'sort'  => 5,
        'module' => 'Customers'
    ],

    //CUSTOMER-ADDRESS
    [
        'key'   => 'customers.address.list',
        'name'  => 'List customers address',
        'route' => 'admin.customer.address.index',
        'sort'  => 1,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.address.store',
        'name'  => 'Create customer address',
        'route' => 'admin.customer.address.store',
        'sort'  => 2,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.address.show',
        'name'  => 'Show Customer address',
        'route' => 'admin.customer.address.show',
        'sort'  => 3,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.address.update',
        'name'  => 'Update Customer address',
        'route' => 'admin.customer.address.update',
        'sort'  => 4,
        'module' => 'Customers'
    ],
    [
        'key'   => 'customers.address.delete',
        'name'  => 'Delete Customer address',
        'route' => 'admin.customer.address.delete',
        'sort'  => 5,
        'module' => 'Customers'
    ],



    //USER
    [
        'key'   => 'admin.roles.list',
        'name'  => 'List roles',
        'route' => 'admin.roles.index',
        'sort'  => 1,
        'module' => 'User'
    ],
    [
        'key'   => 'admin.roles.store',
        'name'  => 'Create role',
        'route' => 'admin.roles.store',
        'sort'  => 2,
        'module' => 'User'
    ],
    [
        'key'   => 'admin.roles.show',
        'name'  => 'Show role',
        'route' => 'admin.roles.show',
        'sort'  => 3,
        'module' => 'User'
    ],
    [
        'key'   => 'admin.roles.update',
        'name'  => 'Update a role',
        'route' => 'admin.roles.update',
        'sort'  => 4,
        'module' => 'User'
    ],
    [
        'key'   => 'admin.roles.delete',
        'name'  => 'Delete a role',
        'route' => 'admin.roles.delete',
        'sort'  => 5,
        'module' => 'User'
    ],

    [
        'key'   => 'admin.users.list',
        'name'  => 'List  admins',
        'route' => 'admin.users.index',
        'sort'  => 1,
        'module' => 'User'
    ],
    [
        'key'   => 'admin.users.store',
        'name'  => 'Create user',
        'route' => 'admin.users.store',
        'sort'  => 2,
        'module' => 'User'
    ],
    [
        'key'   => 'admin.users.show',
        'name'  => 'Show user',
        'route' => 'admin.users.show',
        'sort'  => 3,
        'module' => 'User'
    ],
    [
        'key'   => 'admin.users.update',
        'name'  => 'Update user',
        'route' => 'admin.users.update',
        'sort'  => 4,
        'module' => 'User'
    ],
    [
        'key'   => 'admin.users.delete',
        'name'  => 'Delete user',
        'route' => 'admin.users.delete',
        'sort'  => 5,
        'module' => 'User'
    ],

];

