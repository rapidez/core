<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Rapidez\Core\Models\Store;

class VerifyAdminToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = config('rapidez.admin_token');

        if (Str::startsWith($token, 'base64:')) {
            $token = base64_decode(substr($token, 7));
        }

        abort_if($request->token !== $token, 401);

        return $next($request);
    }
}
