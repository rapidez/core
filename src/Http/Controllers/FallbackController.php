<?php

namespace Rapidez\Core\Http\Controllers;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Illuminate\Routing\Pipeline;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Facades\Rapidez;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class FallbackController extends Controller
{
    protected $container;

    public function __construct(
        ?Container $container,
        protected Router $router
    ) {
        $this->container = $container ?: new Container;
    }

    public function __invoke(Request $request)
    {
        $cacheKey = 'fallbackroute-' . md5($request->url());
        $route = Cache::get($cacheKey);
        if ($route && $response = $this->tryRoute($route['route'], $request)) {
            return $response;
        }

        foreach (Rapidez::getAllFallbackRoutes() as $route) {
            if (! ($response = $this->tryRoute($route['route'], $request))) {
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

    protected function tryRoute(Route $route, $request): ?Response
    {
        try {
            $middleware = $this->router->gatherRouteMiddleware($route->setContainer($this->container));
            /** @var Response $response */
            $response = (new Pipeline($this->container))
                ->send($request)
                ->through($middleware)
                ->then(fn ($request) => $this->router->prepareResponse(
                    $request, $route->bind($request)->run()
                ));

            // Null response is equal to no response or 404.
            if ($response->isNotFound() || (! $response->getContent() && ! $response->isEmpty())) {
                abort(404);
            }

            return $response;
        } catch (RouteNotFoundException|NotFoundHttpException|BackedEnumCaseNotFoundException|ModelNotFoundException|RecordsNotFoundException $e) {
            return null;
        }
    }
}
