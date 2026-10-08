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

    /** @var array<int, array<string, array<string, string>>> */
    protected array $pending = [];

    public function enabled(?Request $request = null): bool
    {
        $request ??= request();

        return config('rapidez.ssr.enabled')
            && class_exists(Browsershot::class)
            && ! str_contains((string) $request->userAgent(), GenerateListingSnapshot::USER_AGENT_TOKEN);
    }

    /**
     * Generate an id based on the definition of a listing, so the same listing on different pages shares the snapshot.
     */
    public function id(string $type, mixed ...$definition): string
    {
        // Unique values per request, like a uniqid() slider reference, would result in a new snapshot every time.
        $definition = preg_replace(
            '/[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}|[0-9a-f]{13}(?![0-9a-f])/i',
            '',
            (string) json_encode($definition, JSON_INVALID_UTF8_SUBSTITUTE),
        );

        return $type . '-' . md5((string) $definition);
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

        if (! is_array($snapshot['parts'] ?? null) || $snapshot['fresh_until'] < now()->getTimestamp()) {
            $this->queueGeneration($key, $id, $request->getPathInfo() . ($query ? '?' . http_build_query($query) : ''));
        }

        return $this->resolved[$id] = $snapshot['parts'] ?? [];
    }

    /**
     * Get a part of the snapshot, resolved with get() when that's not done yet, for
     * example when the part is rendered before the listing. A routed listing,
     * like the category listing, also has to be resolved routed here.
     */
    public function part(string $id, string $part = 'default', bool $routed = false, ?Request $request = null): ?string
    {
        $this->resolved[$id] ??= $this->get($id, $routed, $request);

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

    /**
     * Stop capturing snapshots for the current store for a while, for example when it's misconfigured.
     */
    public function pause(): void
    {
        Cache::put('listing-snapshot-paused-' . config('rapidez.store'), true, now()->addMinutes((int) config('rapidez.ssr.ttl', 60)));
    }

    protected function queueGeneration(string $key, string $id, string $path): void
    {
        if (Cache::has('listing-snapshot-paused-' . config('rapidez.store'))) {
            return;
        }

        // Make sure we're not dispatching the same snapshot multiple times.
        if (! Cache::add($this->lockKey($key), true, now()->addMinutes(5))) {
            return;
        }

        $storeId = (int) config('rapidez.store');

        // Only a web request terminates after rendering the page, so in the console (like a queue worker) dispatch it directly.
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            $this->dispatch([$key => $id], $path, $storeId);

            return;
        }

        // Generate all snapshots of a page with one headless browser after the response.
        if (! $this->pending) {
            app()->terminating(fn () => $this->dispatchPending());
        }

        $this->pending[$storeId][$path][$key] = $id;
    }

    public function dispatchPending(): void
    {
        $pending = $this->pending;
        $this->pending = [];

        foreach ($pending as $storeId => $paths) {
            foreach ($paths as $path => $snapshots) {
                $this->dispatch($snapshots, $path, $storeId, afterResponse: false);
            }
        }
    }

    /**
     * @param  array<string, string>  $snapshots
     */
    protected function dispatch(array $snapshots, string $path, int $storeId, bool $afterResponse = true): void
    {
        $job = GenerateListingSnapshot::dispatch($snapshots, $path, $storeId)->onQueue(config('rapidez.ssr.queue'));

        // Without a queue worker the job runs directly, so not before the response is sent.
        if ($afterResponse && config('queue.default') === 'sync') {
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
