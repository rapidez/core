<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TorMorten\Eventy\Facades\Eventy;

class Uncacheable
{
    public function handle(Request $request, Closure $next)
    {
        Eventy::filter('uncacheable.request', $request);

        $response = $next($request);

        Eventy::filter('uncacheable.response', $response);

        return $response;
    }
}
