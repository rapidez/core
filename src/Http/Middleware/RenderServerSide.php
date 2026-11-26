<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rapidez\Core\Actions\RenderVueServerSide;

class RenderServerSide
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response && is_string($response->getContent())) {
            $response->setContent(app(RenderVueServerSide::class)->replacePlaceholders($response->getContent()));
        }

        return $response;
    }
}
