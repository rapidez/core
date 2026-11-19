<?php

namespace Rapidez\Core\Actions;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Http\Controllers\ConfigController;
use Throwable;

// POC: sends a Vue template (the Blade output) to the Node render server
// and returns the rendered HTML, the search results and timings. Returns
// null when SSR is disabled or the render server fails, so the page
// renders like it always did.
class RenderVueServerSide
{
    public function __invoke(string $template): ?array
    {
        if (! config('rapidez.ssr.enabled')) {
            return null;
        }

        try {
            $start = microtime(true);
            $config = $this->config();

            $response = Http::timeout(config('rapidez.ssr.timeout'))
                ->post(rtrim(config('rapidez.ssr.url'), '/') . '/render', [
                    'url'      => request()->fullUrl(),
                    'template' => $template,
                    'config'   => $config,
                ]);

            if (! $response->successful()) {
                report(new Exception('SSR failed: ' . $response->json('error', $response->body())));

                return null;
            }

            return $response->json() + ['php_total' => round((microtime(true) - $start) * 1000)];
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }

    // The same config as config.js plus the per request frontend config.
    protected function config(): array
    {
        $config = Cache::rememberForever(
            'ssr-config-' . config('rapidez.store') . '-' . Rapidez::getCacheKey(),
            fn () => array_merge(
                app(ConfigController::class)->getExposedConfigValues(),
                app(ConfigController::class)->getConfig(),
            ),
        );

        return array_merge($config, config('frontend') ?? []);
    }
}
