<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
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
    public function handle($request, $next)
    {
        $auth = config('rapidez.health_check.auth', [static::class, 'auth']);
        abort_unless($auth($request), 403);

        return $next($request);
    }

    public static function auth($request)
    {
        return IpUtils::checkIp($request->ip(), config('rapidez.health_check.allowed_ips', ['127.0.0.1/8']));
    }
}
