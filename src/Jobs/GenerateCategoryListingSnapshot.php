<?php

namespace Rapidez\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rapidez\Core\Search\CategoryListingSnapshotStore;
use Spatie\Browsershot\Browsershot;
use Throwable;

/**
 * Visits a category page with a headless browser, waits for the real,
 * client-rendered listing to have results, and stores the rendered markup
 * as an HTML snapshot to be shown to future visitors before Vue boots.
 *
 * This deliberately captures Vue's own output rather than re-implementing the
 * listing in PHP, so the snapshot can never drift from what the real listing
 * looks like.
 */
class GenerateCategoryListingSnapshot implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 60;

    public function __construct(public int $categoryId)
    {
    }

    public function handle(CategoryListingSnapshotStore $store): void
    {
        $categoryModel = config('rapidez.models.category');
        $category = $categoryModel::withoutGlobalScopes()->find($this->categoryId);

        if (! $category) {
            return;
        }

        try {
            $html = $this->render($category->url);
        } catch (Throwable $e) {
            report($e);

            return;
        }

        if (! trim($html)) {
            return;
        }

        $store->put($category, $html);
    }

    protected function render(string $categoryUrl): string
    {
        $browsershot = Browsershot::url(url($categoryUrl))
            ->windowSize(1440, 900)
            ->waitUntilNetworkIdle()
            // `#listing-content` (resources/views/components/listing.blade.php) always
            // exists once Vue mounts the listing, but an actual product means real
            // results made it back from Elasticsearch and got rendered into it.
            ->waitForSelector('[data-testid="listing-item"]', ['timeout' => 15000]);

        if (config('rapidez.listing_snapshot.no_sandbox')) {
            $browsershot->noSandbox();
        }

        if ($nodeBinary = config('rapidez.listing_snapshot.node_binary')) {
            $browsershot->setNodeBinary($nodeBinary);
        }

        if ($npmBinary = config('rapidez.listing_snapshot.npm_binary')) {
            $browsershot->setNpmBinary($npmBinary);
        }

        if ($nodeModulePath = config('rapidez.listing_snapshot.node_module_path')) {
            $browsershot->setNodeModulePath($nodeModulePath);
        }

        if ($chromePath = config('rapidez.listing_snapshot.chrome_path')) {
            $browsershot->setChromePath($chromePath);
        }

        return $browsershot->evaluate("document.getElementById('listing-content').innerHTML");
    }
}
