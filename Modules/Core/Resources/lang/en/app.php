<?php
return [

    'response' => [
        'being-used' => 'This resource :name is getting used in :source',
        'cannot-delete-default' => 'Cannot delete the default :name',
        'fetch-list-success' => ':name list fetched successfully.',
        'fetch-success' => ':name fetched successfully.',
        'create-success' => ':name created successfully.',
        'update-success' => ':name updated successfully.',
        'deleted-success' => ':name deleted successfully.',
        'delete-failed' => 'Error encountered while deleting :name.',
        'last-delete-error' => 'At least one :name is required.',
        'user-define-error' => 'Can not delete system :name',
        'cancel-success' => ':name canceled successfully.',
        'cancel-error' => ':name can not be canceled.',
        'already-created' => 'Already Created',
        'already-taken' => 'The :name has already been taken.',
        'not-found' => ':name not found.',
        'status-change-success' => 'The :name has changed successfully.',
        'status-change-failed' => 'Cannot change the status.',
        'status-updated' => ':name status updated successfully.',
        'type-status-updated' => ':name type updated successfully.',
        'reindex-success' => ':name reindexed successfully.',
        "delete-failed" => ":name could not be deleted.",
        'bulk-reindex-success' => 'All :name reindexed successfully.',
        'attribute-cannot-change' => ':field could not be changed for :type defined attribute.',
        'missing-data' => 'Missing :name translation.',
        'default-set-delete' => 'Default set cannot be deleted.',
        'attribute-groups-present' => 'Attribute Groups present in set.',
        'absolute_path_missing' => 'Absolute path of :name is missing.',
        'value_missing' => 'Value of :name is missing.',
        'absolute_path_not_exist' => 'Absolute path of :name does not exists.',
        'wrong_absolute_path' => 'Wrong absolute path for :name',
        'store_does_not_belong' => 'Store does not belong to :name channel.',
        'use_default_value' => 'Default value must be 1.',
        'no_parent_belong_to_website' => "Patent Category does not belong to this website",
        'inventory_cannot_be_zero' => 'Inventory quantity cannot be less than zero',
        'range-required' => ':name range is required',
        'choose-address' => 'Type of address is required.',
        'please-choose' => 'Please choose :name',
        'cart-merged' => "cart merged successfully",
        "not-enough-stock-quantity" => "stock quantity is not enough",
        "channel-code-required" => "channel code is required",
        'country-not-found' => 'Region does not belongs to country.',
        "invalid-country" => "Invalid :name Country",
        "product-stock-alert" => "You will be notified via Email once the product is available.",
        "verification-success" => "Verification success",
        "already-verified" => "Account already verified",
        "send-confirmation-link" => "Send confirmation link",
        "country-not-allow" => "Country is not allowed",
        'cannot-update-default' => 'Cannot update the default :name',
        'clear-success' => ':name clear successfully',
        'reindex-success' => 'All data reindexed successfully'
    ],
    'users' => [
        'forget-password' => [
            'title' => 'Forget Password',
            'header-title' => 'Recover Password',
            'email' => 'Registered Email',
            'password' => 'Password',
            'confirm-password' => 'Confirm Password',
            'back-link-title' => 'Back to Sign In',
            'submit-btn-title' => 'Send Password Reset Email'
        ],

        'reset-password' => [
            'title' => 'Reset Password',
            'email' => 'Registered Email',
            'password' => 'Password',
            'confirm-password' => 'Confirm Password',
            'back-link-title' => 'Back to Sign In',
            'submit-btn-title' => 'Reset Password',
            'password-reset-success' => 'Password reset successfully.',
        ],

        'roles' => [
            'title' => 'Roles',
            'add-role-title' => 'Add Role',
            'edit-role-title' => 'Edit Role',
            'save-btn-title' => 'Save Role',
            'general' => 'General',
            'name' => 'Name',
            'description' => 'Description',
            'access-control' => 'Access Control',
            'permissions' => 'Permissions',
            'custom' => 'Custom',
            'all' => 'All'
        ],

        'users' => [
            'title' => 'User',
            'add-user-title' => 'Add User',
            'edit-user-title' => 'Edit User',
            'save-btn-title' => 'Save User',
            'general' => 'General',
            'email' => 'Email',
            'name' => 'Name',
            'password' => 'Password',
            'confirm-password' => 'Confirm Password',
            'status-and-role' => 'Status and Role',
            'role' => 'Role',
            'status' => 'Status',
            'account-is-active' => 'Account is Active',
            'current-password' => 'Enter Current Password',
            'confirm-delete' => 'Confirm Delete This Account',
            'confirm-delete-title' => 'Confirm password before delete',
            'delete-last' => 'At least one admin is required.',
            'delete-success' => 'Success! User deleted',
            'incorrect-password' => 'The password you entered is incorrect',
            'password-match' => 'Current password does not match.',
            'account-save' => 'Account changes saved successfully.',
            'login-error' => 'Invalid Email Address or Password.',
            'login-success' => 'Logged in successfully.',
            'logout-success' => 'Logged out successfully.',
            'activate-warning' => 'Your account is yet to be activated, please contact administrator.',
            'already-active' => 'Already Active'
        ],

        'sessions' => [
            'title' => 'Sign In',
            'email' => 'Email',
            'password' => 'Password',
            'forget-password-link-title' => 'Forget Password ?',
            'remember-me' => 'Remember Me',
            'submit-btn-title' => 'Sign In'
        ],

        'token' => [
            'token-missing' => 'Missing token',
            'token-invalid' => 'Invalid token',
            'token-expired' => 'Token is expired',
            'token-generation-problem' => 'Unable to generate token'
        ],

        'auth' => [
            'unauthorised' => 'Unauthorised.',
            'unauthenticated' => 'Unauthenticated.'
        ]
    ],
    'settings' => [
        'locales' => [
            'title' => 'Locales',
            'last-delete-error' => 'At least one Locale is required.',
        ],
        'currencies' => [
            'title' => 'Currency',
            'last-delete-error' => 'At least one Currency is required.',
        ],
    ],
    'exception_message' => [
        'channel-not-found' => "Channel Not Found",
        "cart-id-required" => "cart id is required",
        "product-qty-must-be-above-0" => "product quantity must be greater than 0",
        "not-allowed" => "access denied"
    ],
    "cart" => [
        "product-removed" => "product removed from cart",
        "product-added" => "product added on cart",
        "product-qty-updated" => "product quantity updated on cart",
        "product-remove-due-to-channel-change" => "product removed from cart due to change in channel"
    ],
    "sales" => [
        "payment-transfer-not-allowed" => "Total order must be between :minimum_order_total & :maximum_order_total"
    ]
];
