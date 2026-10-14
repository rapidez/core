<?php

namespace Rapidez\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Search\ListingSnapshotStore;
use RuntimeException;
use Spatie\Browsershot\Browsershot;
use Throwable;

class GenerateListingSnapshot implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const USER_AGENT_TOKEN = 'RapidezSsr';

    public int $tries = 1;

    public int $timeout = 60;

    /**
     * @param  array<string, string>  $snapshots  Cache key => listing id, all on the same page.
     */
    public function __construct(public array $snapshots, public string $path, public int $storeId) {}

    public function handle(ListingSnapshotStore $store): void
    {
        Rapidez::withStore($this->storeId, fn () => $this->generate($store));
    }

    protected function generate(ListingSnapshotStore $store): void
    {
        try {
            $url = rtrim(Rapidez::config('web/secure/base_url') ?: url('/'), '/') . $this->path;
            $result = json_decode($this->render($url), true, flags: JSON_THROW_ON_ERROR);

            if ((string) ($result['store'] ?? '') !== (string) $this->storeId) {
                // All snapshots of this store will end up on the wrong store.
                $store->pause();

                throw new RuntimeException('Captured listing snapshots on ' . $url . ' belong to store "' . ($result['store'] ?? '') . '" instead of "' . $this->storeId . '", make sure web/secure/base_url of the store is its url.');
            }
        } catch (Throwable $e) {
            report($e);
            array_map($store->failed(...), array_keys($this->snapshots));

            return;
        }

        foreach ($this->snapshots as $key => $id) {
            $listing = $result['listings'][$id] ?? [];

            // Not (loaded) on the page for the headless browser, for example because it depends on the visitor.
            // Not stored as the snapshot can be shared with other pages, so we try again later.
            if (! ($listing['loaded'] ?? false)) {
                if (($result['status'] ?? 200) >= 400) {
                    Log::warning('Capturing listing snapshot "' . $id . '" failed with status ' . $result['status'] . ' on ' . $url);
                }

                $store->failed($key);

                continue;
            }

            // Also store an empty result, when there are no results or it's too big, so we're not trying again on every page view.
            $parts = array_filter($listing['parts'] ?? [], fn ($html) => trim($html));
            $parts = ($listing['hits'] ?? 0) && strlen(implode($parts)) < 2_000_000 ? $parts : [];
            $store->put($key, $parts);
            $store->releaseLock($key);
        }
    }

    protected function render(string $url): string
    {
        $ids = json_encode(array_values(array_unique($this->snapshots)));

        $browsershot = Browsershot::url($url)
            ->userAgent('Mozilla/5.0 (compatible; ' . static::USER_AGENT_TOKEN . '/1.0)')
            ->windowSize(1440, 900)
            ->timeout(45)
            // Lazy loaded listings, like the productlist, would otherwise wait until they're idle.
            ->evaluateOnNewDocument("document.addEventListener('vue:mounted', () => { window.ssrMounted = Date.now(); window.\$emit?.('load-lazy') })")
            // Not strict, so open connections like a live chat don't block it.
            ->waitUntilNetworkIdle(false)
            // Wait until the listings are loaded or, for example when it depends on the cart, aren't there at all.
            // When one of them doesn't load at all, we continue with the others.
            ->waitForFunction(strtr(<<<'JS'
                IDS.every((id) => {
                    const selector = `[data-listing-snapshot="${CSS.escape(id)}"]`

                    return document.querySelector(selector + '[data-listing-loaded]')
                        || (! document.querySelector(selector) && (Date.now() - window.ssrMounted > 3000 || performance.now() > 10000))
                }) || performance.now() > 14000
                JS, ['IDS' => $ids]), timeout: 15000);

        if (config('rapidez.ssr.browsershot.no_sandbox')) {
            $browsershot->noSandbox();
        }

        if ($nodeBinary = config('rapidez.ssr.browsershot.node_binary')) {
            $browsershot->setNodeBinary($nodeBinary);
        }

        if ($npmBinary = config('rapidez.ssr.browsershot.npm_binary')) {
            $browsershot->setNpmBinary($npmBinary);
        }

        if ($nodeModulePath = config('rapidez.ssr.browsershot.node_module_path')) {
            $browsershot->setNodeModulePath($nodeModulePath);
        }

        if ($chromePath = config('rapidez.ssr.browsershot.chrome_path')) {
            $browsershot->setChromePath($chromePath);
        }

        return $browsershot->evaluate('(' . <<<'JS'
            async (ids) => {
                // Give async components and teleports within the listings a moment to render.
                let observer
                await new Promise((resolve) => {
                    let timeout = setTimeout(resolve, 200)
                    observer = new MutationObserver(() => {
                        clearTimeout(timeout)
                        timeout = setTimeout(resolve, 200)
                    })
                    observer.observe(document.body, { subtree: true, childList: true, attributes: true })
                    setTimeout(resolve, 3000)
                })
                observer.disconnect()

                const replace = (element, tagName) => {
                    const replacement = document.createElement(tagName)
                    Array.from(element.attributes).forEach((attribute) => replacement.setAttribute(attribute.name, attribute.value))
                    replacement.append(...element.childNodes)
                    element.replaceWith(replacement)

                    return replacement
                }

                const capture = (id) => {
                    const elements = Array.from(document.querySelectorAll(`[data-listing-snapshot="${CSS.escape(id)}"]`))
                    const loaded = elements.find((element) => element.hasAttribute('data-listing-loaded'))
                    const parts = {}

                    elements.forEach((element) => {
                        // The html only contains the attributes, not the current state.
                        element.querySelectorAll('option').forEach((option) => option.toggleAttribute('selected', option.selected))
                        element.querySelectorAll('input[type=checkbox], input[type=radio]').forEach((input) => input.toggleAttribute('checked', input.checked))
                        element.querySelectorAll('input:not([type=checkbox]):not([type=radio])').forEach((input) => input.setAttribute('value', input.value))

                        const part = element.cloneNode(true)

                        // Replace add to cart buttons with anchor links.
                        part.querySelectorAll('[data-testid="listing-item"]').forEach((item) => {
                            const productUrl = item.querySelector('a[href]')?.getAttribute('href')

                            item.querySelectorAll('form button[type="submit"]').forEach((button) => {
                                if (productUrl) {
                                    const link = replace(button, 'a')
                                    link.removeAttribute('type')
                                    link.setAttribute('href', productUrl)
                                }
                            })
                        })

                        part.querySelectorAll('form').forEach((form) => replace(form, 'div'))

                        // Scripts would run again and are already in the real listing.
                        part.querySelectorAll('script').forEach((script) => script.remove())

                        // Keep ID's and radio groups unique while the real listing is rendered next to it.
                        part.querySelectorAll('[id], [for], [name], [data-testid]').forEach((element) => {
                            element.removeAttribute('id')
                            element.removeAttribute('for')
                            element.removeAttribute('name')
                            element.removeAttribute('data-testid')
                        })

                        const name = element.dataset.listingSnapshotPart || 'default'
                        parts[name] = (parts[name] ?? '') + part.innerHTML
                    })

                    return {
                        loaded: !!loaded,
                        hits: Number(loaded?.dataset.listingLoaded ?? 0),
                        parts,
                    }
                }

                return JSON.stringify({
                    store: window.config?.store,
                    status: performance.getEntriesByType('navigation')[0]?.responseStatus,
                    listings: Object.fromEntries(ids.map((id) => [id, capture(id)])),
                })
            }
        JS . ')(' . $ids . ')');
    }
}
