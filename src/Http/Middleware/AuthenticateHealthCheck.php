<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class AuthenticateHealthCheck
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $auth = config('rapidez.healthcheck.auth', [static::class, 'auth']);
        abort_unless($auth($request), 403);

        return $next($request);
    }

    public static function auth(Request $request): bool
    {
        if (! $request->ip()) {
            return false;
        }

        return IpUtils::checkIp($request->ip(), config('rapidez.healthcheck.allowed_ips', ['127.0.0.1/8']));
    }
}
