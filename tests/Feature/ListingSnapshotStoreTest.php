<?php

namespace Rapidez\Core\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Rapidez\Core\Commands\IndexCommand;
use Rapidez\Core\Commands\UpdateIndexCommand;
use Rapidez\Core\Events\IndexStoreAfterEvent;
use Rapidez\Core\Jobs\GenerateListingSnapshot;
use Rapidez\Core\Listeners\FlushListingSnapshots;
use Rapidez\Core\Search\ListingSnapshotStore;
use Rapidez\Core\Tests\TestCase;

class ListingSnapshotStoreTest extends TestCase
{
    protected ListingSnapshotStore $store;

    public function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default'       => 'array',
            'queue.default'       => 'database',
            'rapidez.ssr.enabled' => true,
        ]);

        Queue::fake();

        $this->store = app(ListingSnapshotStore::class);
    }

    #[Test]
    public function it_does_nothing_when_disabled()
    {
        config(['rapidez.ssr.enabled' => false]);
        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);

        $this->assertEquals([], $this->store->get('category-123', request: $this->request()));
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_skips_the_headless_browser_capturing_snapshots()
    {
        $request = $this->request(userAgent: 'Mozilla/5.0 (compatible; ' . GenerateListingSnapshot::USER_AGENT_TOKEN . '/1.0)');

        $this->assertEquals([], $this->store->get('category-123', request: $request));
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_queues_a_single_generation_on_a_miss()
    {
        $this->assertEquals([], $this->store->get('productlist-abc', request: $this->request()));
        $this->assertEquals([], $this->store->get('productlist-abc', request: $this->request()));

        Queue::assertPushed(GenerateListingSnapshot::class, 1);
        Queue::assertPushed(
            GenerateListingSnapshot::class,
            fn ($job) => $job->key === 'productlist-abc'
                && $job->id === 'productlist-abc'
                && $job->path === '/some-page.html'
                && $job->storeId === (int) config('rapidez.store')
        );
    }

    #[Test]
    public function it_queues_again_once_the_lock_is_released()
    {
        $this->store->get('category-123', request: $this->request());
        $this->store->releaseLock('category-123');
        $this->store->get('category-123', request: $this->request());

        Queue::assertPushed(GenerateListingSnapshot::class, 2);
    }

    #[Test]
    public function it_serves_the_parts_of_a_fresh_snapshot()
    {
        $this->store->put('category-123', ['products' => '<div>products</div>', 'filters' => '<div>filters</div>']);

        $this->assertEquals(
            ['products' => '<div>products</div>', 'filters' => '<div>filters</div>'],
            $this->store->get('category-123', request: $this->request())
        );
        $this->assertEquals('<div>filters</div>', $this->store->part('category-123', 'filters'));
        $this->assertNull($this->store->part('category-123', 'unknown'));
        $this->assertNull($this->store->part('category-456', 'filters'));
        Queue::assertNothingPushed();
    }

    #[Test]
    public function an_empty_result_is_not_regenerated_until_stale()
    {
        $this->store->put('productlist-abc', []);

        $this->assertEquals([], $this->store->get('productlist-abc', request: $this->request()));
        Queue::assertNothingPushed();

        $this->travel(61)->minutes();

        $this->store->get('productlist-abc', request: $this->request());
        Queue::assertPushed(GenerateListingSnapshot::class, 1);
    }

    #[Test]
    public function it_serves_a_stale_snapshot_while_regenerating()
    {
        config(['rapidez.ssr.ttl' => 60, 'rapidez.ssr.stale_ttl' => 60]);
        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);

        $this->travel(90)->minutes();

        $this->assertEquals(['default' => '<div>snapshot</div>'], $this->store->get('category-123', request: $this->request()));
        Queue::assertPushed(GenerateListingSnapshot::class, 1);

        $this->travel(60)->minutes();

        $this->assertEquals([], $this->store->get('category-123', request: $this->request()));
    }

    #[Test]
    public function routed_listings_skip_filtered_urls_by_default()
    {
        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);

        $this->assertEquals([], $this->store->get('category-123', true, $this->request('?super_color=red')));
        $this->assertEquals([], $this->store->get('category-123', true, $this->request('?utm_source=newsletter&page=2')));
        $this->assertEquals([], $this->store->get('category-123', true, $this->request('?hits=24')));
        $this->assertEquals(['default' => '<div>snapshot</div>'], $this->store->get('category-123', true, $this->request('?utm_source=newsletter&gclid=abc&unknown=1')));
        Queue::assertNothingPushed();
    }

    #[Test]
    public function routed_listings_get_a_snapshot_per_filter_combination_when_enabled()
    {
        config(['rapidez.ssr.filters' => true]);
        $this->store->put('category-123', ['default' => '<div>unfiltered</div>']);

        $this->assertEquals([], $this->store->get('category-123', true, $this->request('?super_color=blue&hits=24&gclid=abc')));
        Queue::assertPushed(GenerateListingSnapshot::class, function ($job) {
            return $job->id === 'category-123'
                && $job->key === 'category-123-' . md5('hits=24&super_color=blue')
                && $job->path === '/some-page.html?hits=24&super_color=blue';
        });

        $this->store->put('category-123-' . md5('hits=24&super_color=blue'), ['default' => '<div>blue</div>']);

        $this->assertEquals(['default' => '<div>blue</div>'], $this->store->get('category-123', true, $this->request('?super_color=blue&utm_source=newsletter&hits=24')));
        $this->assertEquals(['default' => '<div>unfiltered</div>'], $this->store->get('category-123', true, $this->request()));
    }

    #[Test]
    public function other_listings_ignore_the_url()
    {
        $this->store->put('productlist-abc', ['default' => '<div>snapshot</div>']);

        $this->assertEquals(['default' => '<div>snapshot</div>'], $this->store->get('productlist-abc', request: $this->request('?super_color=red&page=2')));
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_flushes_the_snapshots_of_a_store()
    {
        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);
        $this->store->flush((int) config('rapidez.store') + 1);

        $this->assertEquals(['default' => '<div>snapshot</div>'], $this->store->get('category-123', request: $this->request()));

        $this->store->flush((int) config('rapidez.store'));

        $this->assertEquals([], $this->store->get('category-123', request: $this->request()));
        Queue::assertPushed(GenerateListingSnapshot::class, 1);
    }

    #[Test]
    public function only_a_full_index_flushes_the_snapshots()
    {
        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);

        (new FlushListingSnapshots)->handle(new IndexStoreAfterEvent(new UpdateIndexCommand, (int) config('rapidez.store')));
        $this->assertEquals(['default' => '<div>snapshot</div>'], $this->store->get('category-123', request: $this->request()));

        (new FlushListingSnapshots)->handle(new IndexStoreAfterEvent(new IndexCommand, (int) config('rapidez.store')));
        $this->assertEquals([], $this->store->get('category-123', request: $this->request()));
    }

    #[Test]
    public function it_generates_after_the_response_with_the_sync_queue()
    {
        Bus::fake();
        config(['queue.default' => 'sync']);

        $this->store->get('category-123', request: $this->request());

        Bus::assertDispatchedAfterResponse(GenerateListingSnapshot::class);
    }

    protected function request(string $query = '', string $userAgent = 'Mozilla/5.0'): Request
    {
        return Request::create('/some-page.html' . $query, server: ['HTTP_USER_AGENT' => $userAgent]);
    }
}
