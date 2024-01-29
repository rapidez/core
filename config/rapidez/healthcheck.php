<?php

return [
    // Authenticate using the function passed, if null we will check ips.
    'auth'        => null,
    'allowed_ips' => [
        '127.0.0.1/8',
        ...explode(',', env('HEALTHCHECK_ALLOWED_IPS', '')),
    ],
];
