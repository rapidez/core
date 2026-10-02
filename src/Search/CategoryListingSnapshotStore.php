<?php

namespace Rapidez\Core\Search;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Jobs\GenerateCategoryListingSnapshot;
use Rapidez\Core\Models\Category;
use Spatie\Browsershot\Browsershot;

class CategoryListingSnapshotStore
{
    public function get(Category $category, ?Request $request = null): ?string
    {
        $request ??= request();
        $query = $this->listingQuery($request);

        if (! $this->shouldServe($request, $query)) {
            return null;
        }

        $snapshot = Cache::get($this->snapshotKey($category->entity_id, $query));

        if (! is_array($snapshot) || $snapshot['fresh_until'] < now()->getTimestamp()) {
            $this->queueGeneration($category, $query);
        }

        return is_array($snapshot) ? $snapshot['html'] : null;
    }

    public function put(Category $category, string $html, array $query = []): void
    {
        $ttl = (int) config('rapidez.ssr.ttl', 60);
        $staleTtl = (int) config('rapidez.ssr.stale_ttl', 1440);

        Cache::put(
            $this->snapshotKey($category->entity_id, $query),
            [
                'html'        => $html,
                'fresh_until' => now()->addMinutes($ttl)->getTimestamp(),
            ],
            now()->addMinutes($ttl + $staleTtl),
        );
    }

    public function releaseLock(int $categoryId, array $query = []): void
    {
        Cache::forget($this->lockKey($categoryId, $query));
    }

    protected function shouldServe(Request $request, array $query): bool
    {
        if (! config('rapidez.ssr.enabled') || ! class_exists(Browsershot::class)) {
            return false;
        }

        if (str_contains((string) $request->userAgent(), GenerateCategoryListingSnapshot::USER_AGENT_TOKEN)) {
            return false;
        }

        return ! $query || config('rapidez.ssr.filters');
    }

    protected function listingQuery(Request $request): array
    {
        return Arr::sortRecursive(Arr::only($request->query(), $this->listingParameters()));
    }

    protected function listingParameters(): array
    {
        $filters = collect(config('rapidez.models.attribute')::getCachedWhere(fn ($attribute) => $attribute['filter']))
            ->map(fn ($attribute) => ($attribute['prefix'] ?? '') . $attribute['code']);

        return $filters
            ->concat(config('rapidez.searchkit.range_attributes', []))
            ->concat(Arr::pluck(config('rapidez.searchkit.facet_attributes', []), 'attribute'))
            ->concat(['category', 'q', 'page', 'sort', 'hits'])
            ->unique()
            ->values()
            ->all();
    }

    protected function queueGeneration(Category $category, array $query): void
    {
        // Make sure we're not dispatching the same category multiple times.
        if (! Cache::add($this->lockKey($category->entity_id, $query), true, now()->addMinutes(5))) {
            return;
        }

        GenerateCategoryListingSnapshot::dispatch($category->entity_id, (int) config('rapidez.store'), $query);
    }

    protected function snapshotKey(int $categoryId, array $query): string
    {
        return 'category-listing-snapshot-' . $this->keySuffix($categoryId, $query);
    }

    protected function lockKey(int $categoryId, array $query): string
    {
        return 'category-listing-snapshot-lock-' . $this->keySuffix($categoryId, $query);
    }

    protected function keySuffix(int $categoryId, array $query): string
    {
        // Sorted so the same combination always results in the same snapshot.
        return config('rapidez.store') . '-' . $categoryId . ($query ? '-' . md5(http_build_query(Arr::sortRecursive($query))) : '');
    }
}
