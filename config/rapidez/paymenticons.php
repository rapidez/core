<?php

return [
    // Which disk should be used to save the payment icons?
    // See config/filesystems.php
    'disk' => env('RAPIDEZ_DISK', 'public'),

    // Use this when an icon is not recognized
    'fallback' => 'default',

    // The set of icons to use
    'icons' => [
        'achteraf-betalen',
        'apple-pay',
        'bancontact',
        'bank-transfer',
        'belfius',
        'creditcard',
        'giropay',
        'ideal',
        'kbc',
        'paypal',
        'sofort',
        'visa',
        // ... Add extra icons here (and add the icons to your payment-icons folder)
    ],

    // @ will be replaced by a capture group containing all of the above icons
    'regexes' => [
        '(?:^|[^a-z])@(?:$|[^a-z])',
        '@',
    ],
];
