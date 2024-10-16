<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VerifyAdminToken
{
    public function handle(Request $request, Closure $next): mixed
    {
        $token = config('rapidez.admin_token');

        if (Str::startsWith($token, 'base64:')) {
            $token = base64_decode(substr($token, 7));
        }

        abort_if($request->token !== $token, 401);

        return $next($request);
    }
}
