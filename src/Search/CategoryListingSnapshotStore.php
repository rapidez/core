<?php

namespace Rapidez\Core\Search;

use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Jobs\GenerateCategoryListingSnapshot;
use Rapidez\Core\Models\Category;

/**
 * Serves a cached HTML snapshot of a category's default (unfiltered) listing,
 * captured with a headless browser from the real, fully client-rendered page
 * (see GenerateCategoryListingSnapshot). It's a literal copy of Vue's own
 * output, so there's no separate template to keep in sync with it.
 */
class CategoryListingSnapshotStore
{
    /**
     * Returns the cached snapshot, or null if there isn't one (yet). On a miss,
     * a job is queued to generate one for the *next* visitor - this request
     * just falls back to the regular, fully client-rendered listing.
     */
    public function get(Category $category): ?string
    {
        if (! config('rapidez.listing_snapshot.enabled')) {
            return null;
        }

        $snapshot = Cache::get($this->snapshotKey($category));

        if ($snapshot === null) {
            $this->queueGeneration($category);
        }

        return $snapshot ?: null;
    }

    public function put(Category $category, string $html): void
    {
        Cache::put(
            $this->snapshotKey($category),
            $html,
            now()->addMinutes((int) config('rapidez.listing_snapshot.ttl', 60)),
        );
    }

    protected function queueGeneration(Category $category): void
    {
        // Debounce: while a generation job is pending/running for this category,
        // don't queue another one for every visitor hitting the same cache miss.
        $lockKey = 'category-listing-snapshot-lock-' . config('rapidez.store') . '-' . $category->entity_id;

        if (! Cache::add($lockKey, true, now()->addMinutes(5))) {
            return;
        }

        GenerateCategoryListingSnapshot::dispatch($category->entity_id);
    }

    protected function snapshotKey(Category $category): string
    {
        return 'category-listing-snapshot-' . config('rapidez.store') . '-' . $category->entity_id;
    }
}
