<?php

return [
    // Enable SSR for categories? This requires spatie/browsershot!
    'enabled' => env('RAPIDEZ_SSR', false),

    // Time to serve the snapshots
    'ttl'       => env('RAPIDEZ_SSR_TTL', 60),
    'stale_ttl' => env('RAPIDEZ_SSR_STALE_TTL', 1440),

    // Also save snapshots for filter urls?
    'filters' => env('RAPIDEZ_SSR_FILTERS', false),

    // Browsershot/Puppeteer options, see https://spatie.be/docs/browsershot.
    'browsershot' => [
        'node_binary'      => env('BROWSERSHOT_NODE_BINARY'),
        'npm_binary'       => env('BROWSERSHOT_NPM_BINARY'),
        'node_module_path' => env('BROWSERSHOT_NODE_MODULE_PATH'),
        'chrome_path'      => env('BROWSERSHOT_CHROME_PATH'),
        'no_sandbox'       => env('BROWSERSHOT_NO_SANDBOX', false),
    ],
];
