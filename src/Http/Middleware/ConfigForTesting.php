<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ConfigForTesting
{
    public function handle(Request $request, Closure $next, ...$stores)
    {
        $sessionConfig = $request->session()->get('config', []);

        if ($request->has('checkout')) {
            if ($request->get('checkout') === 'onestep') {
                Arr::set($sessionConfig, 'rapidez.frontend.checkout_steps', ['onestep']);
            } else {
                Arr::forget($sessionConfig, 'rapidez.frontend.checkout_steps');
            }
            $request->session()->put('config', $sessionConfig);
        }

        $config = config();

        foreach ($sessionConfig as $key => $values) {
            $config->set($key, array_merge_recursive(
                $values ?? [], $config->get($key, [])
            ));
        }

        return $next($request);
    }
}
