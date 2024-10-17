<?php

namespace Rapidez\Core\Http\Controllers\Fallback;

use TorMorten\Eventy\Facades\Eventy;

class LegacyFallbackController
{
    public function __invoke(): mixed
    {
        // @phpstan-ignore-next-line
        foreach (Eventy::filter('routes', []) as $route) {
            ob_start();
            require $route;
            if ($output = ob_get_clean()) {
                return $output;
            }
        }

        return null;
    }
}
