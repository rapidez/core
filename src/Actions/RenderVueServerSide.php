<?php

namespace Rapidez\Core\Actions;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Http\Controllers\ConfigController;
use Throwable;

// POC: sends a Vue template (the Blade output of @ssr) to the Node render
// server and returns the rendered HTML in front of the template, with the
// search results pushed to the foot. When SSR is disabled or fails only the
// template is returned, so the page renders like it always did.
class RenderVueServerSide
{
    public function __invoke(string $template): string
    {
        if (! config('rapidez.ssr.enabled') || ! $ssr = $this->render($template)) {
            return $template;
        }

        app('view')->startPush(
            'foot',
            '<script type="application/json" id="ssr-listing-results">'
            . json_encode($ssr['results'][0] ?? null, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
            . '</script>',
        );

        return '<!-- SSR: ' . e(json_encode($ssr['timings'] + ['php_total' => $ssr['php_total']])) . ' -->'
            . '<div v-pre data-ssr-listing>' . $ssr['html'] . '</div>'
            . $template;
    }

    protected function render(string $template): ?array
    {
        try {
            $start = microtime(true);

            $response = Http::timeout(config('rapidez.ssr.timeout'))
                ->post(rtrim(config('rapidez.ssr.url'), '/') . '/render', [
                    'url'      => request()->fullUrl(),
                    'template' => $template,
                    'config'   => $this->config(),
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
        // TODO: Replace with a frontend config hook, this runs the layout composers twice.
        if (! request()->attributes->get('rapidez-ssr-composed')) {
            request()->attributes->set('rapidez-ssr-composed', true);
            app('view')->callComposer(view('rapidez::layouts.app'));
        }

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
