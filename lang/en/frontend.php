<?php

return [
    'cart' => [
        'add'    => 'added to the cart.',
        'remove' => 'removed from the cart.',
        'coupon' => [
            'applied' => 'Coupon applied succesfully!',
        ],
    ],
    'errors' => [
        'wrong'           => 'Something went wrong, please try again.',
        'session_expired' => 'Your session expired, please login again.',
        'cart_expired'    => 'Your cart expired, please re-add the products.',
    ],
    'checkout' => [
        'no_shipping_method' => 'No shipping method selected.',
        'no_payment_method'  => 'No payment method selected.',
    ],
    'account' => [
        'password_mismatch' => 'Please make sure your passwords match.',
        'email_password'    => 'You did not specify an email or password.',
        'email'             => 'An email address is required.',
    ],

    'filters' => [
        'no'  => 'No',
        'yes' => 'Yes',
    ],

    'asc'       => 'ascending',
    'desc'      => 'descending',
    'relevance' => 'Relevance',
    'all'       => 'All',

    'sorting' => [
        'created_at' => [
            'asc'  => 'Oldest',
            'desc' => 'Newest',
        ],
        'name' => [
            'asc'  => 'Name A-Z',
            'desc' => 'Name Z-A',
        ],
    ],
];
