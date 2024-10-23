<?php

namespace Rapidez\Core\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Facades\Rapidez;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class FallbackController
{
    public function __invoke(Request $request): mixed
    {
        $cacheKey = 'fallbackroute-' . md5($request->url());
        $route = Cache::get($cacheKey);
        if ($route && $response = $this->tryRoute($route)) {
            return $response;
        }

        foreach (Rapidez::getAllFallbackRoutes() as $route) {
            if (! ($response = $this->tryRoute($route))) {
                continue;
            }

            try {
                Cache::put($cacheKey, $route, value(config('rapidez.routing.fallback.cache_duration', 3600), $request, $response, $route));
            } catch (Exception $e) {
                // We can't cache it, no worries.
                // Ususally a sign it's a direct callback or caching hasn't been configured properly.
            }

            return $response;
        }

        abort(404);
    }

    /** @param array<string, mixed> $route */
    protected function tryRoute(array $route): mixed
    {
        try {
            $response = App::call($route['action']['uses']);

            // Null response is equal to no response or 404.
            if ($response === null) {
                abort(404);
            }

            return $response;
        } catch (RouteNotFoundException|NotFoundHttpException|BackedEnumCaseNotFoundException|ModelNotFoundException|RecordsNotFoundException $e) {
            return null;
        }
    }
}
