<?php

namespace Rapidez\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Search\CategoryListingSnapshotStore;
use RuntimeException;
use Spatie\Browsershot\Browsershot;
use Throwable;

class GenerateCategoryListingSnapshot implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const USER_AGENT_TOKEN = 'RapidezSsr';

    public int $tries = 1;

    public int $timeout = 60;

    public function __construct(public int $categoryId, public int $storeId, public array $query = []) {}

    public function handle(CategoryListingSnapshotStore $store): void
    {
        Rapidez::withStore($this->storeId, function () use ($store) {
            if ($this->generate($store)) {
                $store->releaseLock($this->categoryId, $this->query);
            }
        });
    }

    protected function generate(CategoryListingSnapshotStore $store): bool
    {
        $categoryModel = config('rapidez.models.category');
        $category = $categoryModel::withoutGlobalScopes()->find($this->categoryId);

        if (! $category) {
            return false;
        }

        try {
            $url = url($category->url) . ($this->query ? '?' . http_build_query($this->query) : '');
            $result = json_decode($this->render($url), true, flags: JSON_THROW_ON_ERROR);

            throw_if(
                (string) ($result['store'] ?? '') !== (string) $this->storeId,
                RuntimeException::class,
                'Captured listing snapshot of category ' . $this->categoryId . ' belongs to store "' . ($result['store'] ?? '') . '" instead of "' . $this->storeId . '", make sure the url resolves to the right store.'
            );
        } catch (Throwable $e) {
            report($e);

            return false;
        }

        if (! ($result['items'] ?? 0) || ! trim($result['html'] ?? '')) {
            return false;
        }

        $store->put($category, $result['html'], $this->query);

        return true;
    }

    protected function render(string $categoryUrl): string
    {
        $browsershot = Browsershot::url($categoryUrl)
            ->userAgent('Mozilla/5.0 (compatible; ' . static::USER_AGENT_TOKEN . '/1.0)')
            ->windowSize(1440, 900)
            ->waitUntilNetworkIdle()
            ->waitForSelector('#listing-content[data-listing-loaded]', ['timeout' => 15000]);

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

        return $browsershot->evaluate(<<<'JS'
            (() => {
                const listing = document.getElementById('listing-content').cloneNode(true)
                const items = listing.querySelectorAll('[data-testid="listing-item"]')

                const replace = (element, tagName) => {
                    const replacement = document.createElement(tagName)
                    Array.from(element.attributes).forEach((attribute) => replacement.setAttribute(attribute.name, attribute.value))
                    replacement.append(...element.childNodes)
                    element.replaceWith(replacement)

                    return replacement
                }

                // Replace add to cart buttons with anchor links.
                items.forEach((item) => {
                    const productUrl = item.querySelector('a[href]')?.getAttribute('href')

                    item.querySelectorAll('form button[type="submit"]').forEach((button) => {
                        if (productUrl) {
                            const link = replace(button, 'a')
                            link.removeAttribute('type')
                            link.setAttribute('href', productUrl)
                        }
                    })
                })

                listing.querySelectorAll('form').forEach((form) => replace(form, 'div'))

                // Remove ID's to keep them unique when Vue kicks in.
                listing.querySelectorAll('[id], [for], [data-testid]').forEach((element) => {
                    element.removeAttribute('id')
                    element.removeAttribute('for')
                    element.removeAttribute('data-testid')
                })

                return JSON.stringify({ store: window.config?.store, items: items.length, html: listing.innerHTML })
            })()
        JS);
    }
}
