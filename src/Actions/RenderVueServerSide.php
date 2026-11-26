<?php

namespace Rapidez\Core\Actions;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Http\Controllers\ConfigController;
use Throwable;

// POC: collects Vue templates (the Blade output) while the page renders and
// returns an id for the placeholders. When the page is done, and with that
// the frontend config from the view composers, the RenderServerSide
// middleware sends them to the Node render server and swaps the placeholders
// for the rendered HTML and search results. When SSR fails the placeholders
// are removed, so the page renders like it always did.
class RenderVueServerSide
{
    protected array $templates = [];

    public function __invoke(string $template): ?int
    {
        if (! config('rapidez.ssr.enabled')) {
            return null;
        }

        $this->templates[] = $template;

        return array_key_last($this->templates);
    }

    public function replacePlaceholders(string $html): string
    {
        foreach ($this->templates as $id => $template) {
            $ssr = $this->render($template);

            $html = str_replace(
                ["<!-- rapidez-ssr:{$id} -->", "<!-- rapidez-ssr-results:{$id} -->"],
                $ssr ? [
                    '<!-- SSR: ' . e(json_encode($ssr['timings'] + ['php_total' => $ssr['php_total']])) . ' -->'
                    . '<div v-pre data-ssr-listing>' . $ssr['html'] . '</div>',
                    '<script type="application/json" id="ssr-listing-results">'
                    . json_encode($ssr['results'][0] ?? null, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
                    . '</script>',
                ] : ['', ''],
                $html,
            );
        }

        $this->templates = [];

        return $html;
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
