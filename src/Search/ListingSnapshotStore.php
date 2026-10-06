<?php

namespace Rapidez\Core\Search;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Jobs\GenerateListingSnapshot;
use Spatie\Browsershot\Browsershot;

class ListingSnapshotStore
{
    /** @var array<string, array<string, string>> */
    protected array $resolved = [];

    public function enabled(?Request $request = null): bool
    {
        $request ??= request();

        return config('rapidez.ssr.enabled')
            && class_exists(Browsershot::class)
            && ! str_contains((string) $request->userAgent(), GenerateListingSnapshot::USER_AGENT_TOKEN);
    }

    /**
     * Get the snapshot parts (part => html) of a listing and queue a (re)generation when needed.
     * Routed listings, like the category listing, get a snapshot per filter combination.
     *
     * @return array<string, string>
     */
    public function get(string $id, bool $routed = false, ?Request $request = null): array
    {
        $request ??= request();

        if (! $this->enabled($request)) {
            return [];
        }

        $query = $routed ? $this->listingQuery($request) : [];

        if ($query && ! config('rapidez.ssr.filters')) {
            return [];
        }

        $key = $id . ($query ? '-' . md5(http_build_query($query)) : '');
        $snapshot = Cache::get($this->snapshotKey($key));

        if (! is_array($snapshot) || ! isset($snapshot['parts']) || $snapshot['fresh_until'] < now()->getTimestamp()) {
            $this->queueGeneration($key, $id, $request->getPathInfo() . ($query ? '?' . http_build_query($query) : ''));
        }

        return $this->resolved[$id] = $snapshot['parts'] ?? [];
    }

    public function part(string $id, string $part = 'default'): ?string
    {
        return $this->resolved[$id][$part] ?? null;
    }

    /**
     * @param  array<string, string>  $parts
     */
    public function put(string $key, array $parts): void
    {
        $ttl = (int) config('rapidez.ssr.ttl', 60);
        $staleTtl = (int) config('rapidez.ssr.stale_ttl', 1440);

        Cache::put(
            $this->snapshotKey($key),
            [
                'parts'       => $parts,
                'fresh_until' => now()->addMinutes($ttl)->getTimestamp(),
            ],
            now()->addMinutes($ttl + $staleTtl),
        );
    }

    public function flush(int $storeId): void
    {
        Cache::forever($this->versionKey($storeId), $this->version($storeId) + 1);
    }

    public function releaseLock(string $key): void
    {
        Cache::forget($this->lockKey($key));
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

    protected function queueGeneration(string $key, string $id, string $path): void
    {
        // Make sure we're not dispatching the same snapshot multiple times.
        if (! Cache::add($this->lockKey($key), true, now()->addMinutes(5))) {
            return;
        }

        $job = GenerateListingSnapshot::dispatch($key, $id, $path, (int) config('rapidez.store'));

        if (config('queue.default') === 'sync') {
            $job->afterResponse();
        }
    }

    protected function snapshotKey(string $key): string
    {
        return 'listing-snapshot-' . $this->version((int) config('rapidez.store')) . '-' . config('rapidez.store') . '-' . $key;
    }

    protected function lockKey(string $key): string
    {
        return 'listing-snapshot-lock-' . config('rapidez.store') . '-' . $key;
    }

    protected function version(int $storeId): int
    {
        return (int) Cache::get($this->versionKey($storeId), 0);
    }

    protected function versionKey(int $storeId): string
    {
        return 'listing-snapshot-version-' . $storeId;
    }
}
