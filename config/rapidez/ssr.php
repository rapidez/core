<?php

return [
    // POC: render listings server side with the Node render server.
    // Build it with: node_modules/.bin/vite build --config vite.config.ssr.js
    // Run it with: node bootstrap/ssr/server.js
    'enabled' => env('RAPIDEZ_SSR', false),

    'url' => env('RAPIDEZ_SSR_URL', 'http://127.0.0.1:13714'),

    // Seconds to wait for the render server before rendering without SSR.
    'timeout' => env('RAPIDEZ_SSR_TIMEOUT', 2),
];
