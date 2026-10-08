<?php

return [
    // Enable SSR for listings? This requires spatie/browsershot!
    'enabled' => env('RAPIDEZ_SSR', false),

    // Also for productlists like sliders?
    'productlists' => env('RAPIDEZ_SSR_PRODUCTLISTS', false),

    // Also save snapshots for filter urls?
    'filters' => env('RAPIDEZ_SSR_FILTERS', false),

    // Time to serve the snapshots
    'ttl'       => env('RAPIDEZ_SSR_TTL', 60),
    'stale_ttl' => env('RAPIDEZ_SSR_STALE_TTL', 1440),

    // Queue to capture the snapshots on, as it uses a headless browser.
    'queue' => env('RAPIDEZ_SSR_QUEUE'),

    // Seconds a full page cache, like Statamic static caching, waits for the snapshots
    // being generated so they're included. Within that time the page isn't cached.
    'cache_wait' => env('RAPIDEZ_SSR_CACHE_WAIT', 120),

    // Browsershot/Puppeteer options, see https://spatie.be/docs/browsershot.
    'browsershot' => [
        'node_binary'      => env('BROWSERSHOT_NODE_BINARY'),
        'npm_binary'       => env('BROWSERSHOT_NPM_BINARY'),
        'node_module_path' => env('BROWSERSHOT_NODE_MODULE_PATH'),
        'chrome_path'      => env('BROWSERSHOT_CHROME_PATH'),
        'no_sandbox'       => env('BROWSERSHOT_NO_SANDBOX', false),
    ],
];
