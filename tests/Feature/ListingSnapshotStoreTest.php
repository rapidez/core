<?php

namespace Rapidez\Core\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Rapidez\Core\Commands\IndexCommand;
use Rapidez\Core\Commands\UpdateIndexCommand;
use Rapidez\Core\Events\IndexStoreAfterEvent;
use Rapidez\Core\Jobs\GenerateListingSnapshot;
use Rapidez\Core\Listeners\FlushListingSnapshots;
use Rapidez\Core\Search\ListingSnapshotStore;
use Rapidez\Core\Tests\TestCase;
use TorMorten\Eventy\Facades\Eventy;

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
        $this->app->terminate();
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_skips_the_headless_browser_capturing_snapshots()
    {
        $request = $this->request(userAgent: 'Mozilla/5.0 (compatible; ' . GenerateListingSnapshot::USER_AGENT_TOKEN . '/1.0)');

        $this->assertEquals([], $this->store->get('category-123', request: $request));
        $this->app->terminate();
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_queues_a_single_generation_on_a_miss()
    {
        $this->assertEquals([], $this->store->get('productlist-abc', request: $this->request()));
        $this->assertEquals([], $this->store->get('productlist-abc', request: $this->request()));

        $this->app->terminate();
        Queue::assertPushed(GenerateListingSnapshot::class, 1);
        Queue::assertPushed(
            GenerateListingSnapshot::class,
            fn ($job) => $job->snapshots === ['productlist-abc' => 'productlist-abc']
                && $job->path === '/some-page.html'
                && $job->storeId === (int) config('rapidez.store')
        );
    }

    #[Test]
    public function it_queues_on_the_configured_queue()
    {
        config(['rapidez.ssr.queue' => 'snapshots']);

        $this->store->get('category-123', request: $this->request());

        $this->app->terminate();
        Queue::assertPushedOn('snapshots', GenerateListingSnapshot::class);
    }

    #[Test]
    public function it_queues_again_once_the_lock_is_released()
    {
        $this->store->get('category-123', request: $this->request());
        $this->app->terminate();
        $this->store->releaseLock('category-123');
        $this->store->get('category-123', request: $this->request());

        $this->app->terminate();
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
        $this->app->terminate();
        Queue::assertNothingPushed();
    }

    #[Test]
    public function a_part_resolves_the_snapshot_when_rendered_before_the_listing()
    {
        $this->store->put('category-123', ['products' => '<div>products</div>']);

        $this->assertEquals('<div>products</div>', $this->store->part('category-123', 'products', request: $this->request()));
        $this->assertNull($this->store->part('category-456', 'products', request: $this->request()));
        $this->assertNull($this->store->part('category-456', 'filters', request: $this->request()));
        $this->app->terminate();
        Queue::assertPushed(GenerateListingSnapshot::class, 1);
        Queue::assertPushed(GenerateListingSnapshot::class, fn ($job) => $job->snapshots === ['category-456' => 'category-456']);
    }

    #[Test]
    public function a_routed_part_respects_the_filters()
    {
        $this->store->put('category-123', ['products' => '<div>unfiltered</div>']);

        $this->assertNull($this->store->part('category-123', 'products', true, $this->request('?super_color=red')));
        $this->assertEquals('<div>unfiltered</div>', (new ListingSnapshotStore)->part('category-123', 'products', request: $this->request('?super_color=red')));
        $this->app->terminate();
        Queue::assertNothingPushed();
    }

    #[Test]
    public function an_empty_result_is_not_regenerated_until_stale()
    {
        $this->store->put('productlist-abc', []);

        $this->assertEquals([], $this->store->get('productlist-abc', request: $this->request()));
        $this->app->terminate();
        Queue::assertNothingPushed();

        $this->travel(61)->minutes();

        $this->store->get('productlist-abc', request: $this->request());
        $this->app->terminate();
        Queue::assertPushed(GenerateListingSnapshot::class, 1);
    }

    #[Test]
    public function it_serves_a_stale_snapshot_while_regenerating()
    {
        config(['rapidez.ssr.ttl' => 60, 'rapidez.ssr.stale_ttl' => 60]);
        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);

        $this->travel(90)->minutes();

        $this->assertEquals(['default' => '<div>snapshot</div>'], $this->store->get('category-123', request: $this->request()));
        $this->app->terminate();
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
        $this->app->terminate();
        Queue::assertNothingPushed();
    }

    #[Test]
    public function routed_listings_get_a_snapshot_per_filter_combination_when_enabled()
    {
        config(['rapidez.ssr.filters' => true]);
        $this->store->put('category-123', ['default' => '<div>unfiltered</div>']);

        $this->assertEquals([], $this->store->get('category-123', true, $this->request('?super_color=blue&hits=24&gclid=abc')));
        $this->app->terminate();
        Queue::assertPushed(GenerateListingSnapshot::class, function ($job) {
            return $job->snapshots === ['category-123-' . md5('hits=24&super_color=blue') => 'category-123']
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
        $this->app->terminate();
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
        $this->app->terminate();
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
    public function it_generates_after_the_response()
    {
        Bus::fake();
        config(['queue.default' => 'sync']);

        $this->store->get('category-123', request: $this->request());
        Bus::assertNothingDispatched();

        $this->app->terminate();
        Bus::assertDispatched(GenerateListingSnapshot::class);
        Bus::assertNotDispatchedAfterResponse(GenerateListingSnapshot::class);
    }

    #[Test]
    public function it_generates_the_snapshots_of_a_page_together()
    {
        $this->store->get('category-123', true, $this->request());
        $this->store->get('productlist-abc', request: $this->request());
        $this->store->get('productlist-def', request: Request::create('/other-page.html', server: ['HTTP_USER_AGENT' => 'Mozilla/5.0']));

        $this->app->terminate();
        Queue::assertPushed(GenerateListingSnapshot::class, 2);
        Queue::assertPushed(
            GenerateListingSnapshot::class,
            fn ($job) => $job->snapshots === ['category-123' => 'category-123', 'productlist-abc' => 'productlist-abc'] && $job->path === '/some-page.html'
        );
        $this->app->terminate();
        Queue::assertPushed(
            GenerateListingSnapshot::class,
            fn ($job) => $job->snapshots === ['productlist-def' => 'productlist-def'] && $job->path === '/other-page.html'
        );
    }

    #[Test]
    public function the_id_ignores_unique_values_per_request()
    {
        $slot = fn () => '<div ref="' . uniqid('slider') . '" data-id="' . str()->uuid() . '">MS04</div>';

        $this->assertEquals($this->store->id('productlist', ['MS04'], $slot()), $this->store->id('productlist', ['MS04'], $slot()));
        $this->assertNotEquals($this->store->id('productlist', ['MS04'], $slot()), $this->store->id('productlist', ['MS05'], $slot()));
        $this->assertStringStartsWith('productlist-', $this->store->id('productlist', ['MS04']));
    }

    #[Test]
    public function it_stops_capturing_when_paused()
    {
        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);
        $this->travel(61)->minutes();
        $this->store->pause();

        $this->assertEquals(['default' => '<div>snapshot</div>'], $this->store->get('category-123', request: $this->request()));
        $this->assertEquals([], $this->store->get('category-456', request: $this->request()));
        $this->app->terminate();
        Queue::assertNothingPushed();

        $this->travel(61)->minutes();
        $this->store->get('category-456', request: $this->request());
        $this->app->terminate();
        Queue::assertPushed(GenerateListingSnapshot::class, 1);
    }

    #[Test]
    public function a_part_within_a_listing_is_routed_by_default()
    {
        $directory = sys_get_temp_dir() . '/listing-snapshot-test';
        @mkdir($directory);
        file_put_contents($directory . '/listing.blade.php', "@props(['snapshot' => null])\n<x-rapidez::listing-snapshot part=\"products\"/>");
        Blade::anonymousComponentPath($directory, 'test');

        $this->store->put('category-123', ['products' => '<div>unfiltered</div>']);

        $this->app->instance('request', $this->request());
        $this->assertStringContainsString('<div>unfiltered</div>', Blade::render('<x-test::listing snapshot="category-123"/>'));

        $this->app->forgetScopedInstances();
        $this->app->instance('request', $this->request('?super_color=red'));
        $this->assertStringNotContainsString('<div>unfiltered</div>', Blade::render('<x-test::listing snapshot="category-123"/>'));

        // An explicit id isn't routed by default, like a productlist.
        $this->app->forgetScopedInstances();
        $this->assertStringContainsString('<div>unfiltered</div>', Blade::render('<x-rapidez::listing-snapshot id="category-123" part="products"/>'));
    }

    #[Test]
    public function a_page_is_not_cacheable_while_its_snapshots_are_generated()
    {
        $this->assertTrue($this->store->cacheable($this->request()));

        $this->store->get('category-123', request: $this->request());
        $this->assertFalse($this->store->cacheable($this->request()));

        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);
        $this->store->releaseLock('category-123');
        $this->app->forgetScopedInstances();

        $store = app(ListingSnapshotStore::class);
        $store->get('category-123', request: $this->request());
        $this->assertTrue($store->cacheable($this->request()));
    }

    #[Test]
    public function a_page_is_cacheable_when_generating_fails_or_takes_too_long()
    {
        config(['rapidez.ssr.cache_wait' => 120]);

        $this->store->get('category-123', request: $this->request());
        $this->store->get('category-456', request: $this->request());
        $this->store->failed('category-123');
        $this->app->forgetScopedInstances();

        $store = app(ListingSnapshotStore::class);
        $store->get('category-123', request: $this->request());
        $this->assertTrue($store->cacheable($this->request()));

        $store->get('category-456', request: $this->request());
        $this->assertFalse($store->cacheable($this->request()));

        $this->travel(121)->seconds();
        $this->app->forgetScopedInstances();

        $store = app(ListingSnapshotStore::class);
        $store->get('category-456', request: $this->request());
        $this->assertTrue($store->cacheable($this->request()));
    }

    #[Test]
    public function a_page_with_a_stale_or_empty_snapshot_is_cacheable()
    {
        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);
        $this->store->put('productlist-abc', []);
        $this->travel(61)->minutes();

        $this->store->get('category-123', request: $this->request());
        $this->store->get('productlist-abc', request: $this->request());
        $this->app->terminate();

        Queue::assertPushed(GenerateListingSnapshot::class, 1);
        $this->assertTrue($this->store->cacheable($this->request()));
    }

    #[Test]
    public function the_page_captured_by_the_headless_browser_is_not_cacheable()
    {
        $this->assertFalse($this->store->cacheable($this->request(userAgent: 'Mozilla/5.0 (compatible; ' . GenerateListingSnapshot::USER_AGENT_TOKEN . '/1.0)')));

        config(['rapidez.ssr.enabled' => false]);
        $this->assertTrue($this->store->cacheable($this->request(userAgent: 'Mozilla/5.0 (compatible; ' . GenerateListingSnapshot::USER_AGENT_TOKEN . '/1.0)')));
    }

    #[Test]
    public function the_response_is_marked_uncacheable_while_generating()
    {
        Eventy::addFilter('uncacheable.response', function ($response) {
            $response->headers->set('X-Uncacheable', 'true');

            return $response;
        });

        Route::get('/listing-page', fn () => Blade::render('<x-rapidez::listing-snapshot id="category-123"/>'));

        $this->get('/listing-page')->assertHeader('X-Uncacheable');

        $this->store->put('category-123', ['default' => '<div>snapshot</div>']);
        $this->store->releaseLock('category-123');
        $this->app->forgetScopedInstances();

        $this->get('/listing-page')->assertHeaderMissing('X-Uncacheable')->assertSee('<div>snapshot</div>', false);
    }

    protected function request(string $query = '', string $userAgent = 'Mozilla/5.0'): Request
    {
        return Request::create('/some-page.html' . $query, server: ['HTTP_USER_AGENT' => $userAgent]);
    }
}
