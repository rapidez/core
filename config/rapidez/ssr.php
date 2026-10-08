<?php

return [
    // Enable SSR for listings? This requires spatie/browsershot!
    'enabled' => env('RAPIDEZ_SSR', false),

    // Also for productlists? Disable it per productlist with: <x-rapidez::productlist :snapshot="false" ...>
    'productlists' => env('RAPIDEZ_SSR_PRODUCTLISTS', false),

    // Time to serve the snapshots
    'ttl'       => env('RAPIDEZ_SSR_TTL', 60),
    'stale_ttl' => env('RAPIDEZ_SSR_STALE_TTL', 1440),

    // Also save snapshots for filter urls?
    'filters' => env('RAPIDEZ_SSR_FILTERS', false),

    // Queue to capture the snapshots on, as it uses a headless browser.
    'queue' => env('RAPIDEZ_SSR_QUEUE'),

    // Browsershot/Puppeteer options, see https://spatie.be/docs/browsershot.
    'browsershot' => [
        'node_binary'      => env('BROWSERSHOT_NODE_BINARY'),
        'npm_binary'       => env('BROWSERSHOT_NPM_BINARY'),
        'node_module_path' => env('BROWSERSHOT_NODE_MODULE_PATH'),
        'chrome_path'      => env('BROWSERSHOT_CHROME_PATH'),
        'no_sandbox'       => env('BROWSERSHOT_NO_SANDBOX', false),
    ],
];
