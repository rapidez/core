<?php

return [
    // Anchor category pages get a static HTML snapshot -  captured with a headless
    // browser from the real, fully Vue-rendered page - shown until Vue itself has
    // taken over. It's a literal copy of Vue's own output, so there's no separate
    // template to keep in sync with the real listing.
    //
    // This requires `spatie/browsershot` with Node and a Chrome/Chromium binary
    // available wherever the queue worker runs, so it's opt-in.
    'enabled' => env('RAPIDEZ_LISTING_SNAPSHOT', false),

    // How long a captured snapshot is served before it's considered stale and a
    // new one gets queued for the next visitor.
    'ttl' => env('RAPIDEZ_LISTING_SNAPSHOT_TTL', 60),

    // Browsershot/Puppeteer options, see https://spatie.be/docs/browsershot.
    'node_binary'      => env('BROWSERSHOT_NODE_BINARY'),
    'npm_binary'       => env('BROWSERSHOT_NPM_BINARY'),
    'node_module_path' => env('BROWSERSHOT_NODE_MODULE_PATH'),
    'chrome_path'      => env('BROWSERSHOT_CHROME_PATH'),

    // Usually needed when Chrome runs as root, e.g. in most Docker containers.
    'no_sandbox' => env('BROWSERSHOT_NO_SANDBOX', false),
];
