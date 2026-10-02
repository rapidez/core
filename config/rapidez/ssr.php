<?php

return [
    // Anchor category pages get a static HTML snapshot -  captured with a headless
    // browser from the real, fully Vue-rendered page - shown until Vue itself has
    // taken over. It's a literal copy of Vue's own output, so there's no separate
    // template to keep in sync with the real listing.
    //
    // This requires `spatie/browsershot` with Node and a Chrome/Chromium binary
    // available wherever the queue worker runs, so it's opt-in. Every store is
    // captured on its own url, so in a multi store setup `url()` needs to resolve
    // to the right domain per store, e.g. with `rapidez.magento_url_from_db`.
    'enabled' => env('RAPIDEZ_SSR', false),

    // How long (in minutes) a captured snapshot is considered fresh. After that a
    // new one gets queued, while the stale one is still served for `stale_ttl`
    // more minutes so visitors don't miss out while it's being regenerated.
    'ttl'       => env('RAPIDEZ_SSR_TTL', 60),
    'stale_ttl' => env('RAPIDEZ_SSR_STALE_TTL', 1440),

    // By default only the unfiltered listing is captured, so no snapshot is shown when
    // the url has filters, sorting, pagination, ... in it; other query parameters, like
    // `utm_*` ones, are ignored. When enabled, every combination gets its own snapshot.
    // Keep in mind that every unique combination results in a capture with a headless
    // browser and a cache entry.
    'filters' => env('RAPIDEZ_SSR_FILTERS', false),

    // Browsershot/Puppeteer options, see https://spatie.be/docs/browsershot.
    'browsershot' => [
        'node_binary'      => env('BROWSERSHOT_NODE_BINARY'),
        'npm_binary'       => env('BROWSERSHOT_NPM_BINARY'),
        'node_module_path' => env('BROWSERSHOT_NODE_MODULE_PATH'),
        'chrome_path'      => env('BROWSERSHOT_CHROME_PATH'),

        // Usually needed when Chrome runs as root, e.g. in most Docker containers.
        'no_sandbox' => env('BROWSERSHOT_NO_SANDBOX', false),
    ],
];
