<?php

namespace Rapidez\Core\Http\Controllers\Fallback;

use Illuminate\Http\Request;
use Rapidez\Core\FallbackRoutesRepository;
use TorMorten\Eventy\Facades\Eventy;

class LegacyFallbackController
{
    public function __invoke(Request $request, FallbackRoutesRepository $routeRepository)
    {
        foreach (Eventy::filter('routes', []) as $route) {
            ob_start();
            require $route;
            if ($output = ob_get_clean()) {
                return $output;
            }
        }
    }
}
