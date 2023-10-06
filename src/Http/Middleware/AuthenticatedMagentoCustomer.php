<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rapidez\Core\Actions\DecodeJwt;

class AuthenticatedMagentoCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->get('token');
        abort_unless($token, 401);

        if (DecodeJwt::isJwt($token)) {
            $authId = DecodeJwt::decode($token)
                ->claims()
                ->get('uid');
        } else {
            $authId = DB::table('oauth_token')
                ->where('token', $token)
                ->where('revoked', 0)
                ->value('customer_id');
        }

        abort_unless($authId, 401);

        auth()->setUser(config('rapidez.models.customer')::findOr($authId, function () {
            abort(401);
        }));

        return $next($request);
    }
}
