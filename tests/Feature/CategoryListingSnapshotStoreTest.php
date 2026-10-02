<?php

namespace Rapidez\Core\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Rapidez\Core\Commands\IndexCommand;
use Rapidez\Core\Commands\UpdateIndexCommand;
use Rapidez\Core\Events\IndexStoreAfterEvent;
use Rapidez\Core\Jobs\GenerateCategoryListingSnapshot;
use Rapidez\Core\Listeners\FlushListingSnapshots;
use Rapidez\Core\Models\Category;
use Rapidez\Core\Search\CategoryListingSnapshotStore;
use Rapidez\Core\Tests\TestCase;

class CategoryListingSnapshotStoreTest extends TestCase
{
    protected CategoryListingSnapshotStore $store;

    protected Category $category;

    public function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default'       => 'array',
            'queue.default'       => 'database',
            'rapidez.ssr.enabled' => true,
        ]);

        Queue::fake();

        $this->store = new CategoryListingSnapshotStore;
        $this->category = (new Category)->forceFill(['entity_id' => 123]);
    }

    #[Test]
    public function it_does_nothing_when_disabled()
    {
        config(['rapidez.ssr.enabled' => false]);
        $this->store->put($this->category, '<div>snapshot</div>');

        $this->assertNull($this->store->get($this->category, $this->request()));
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_queues_a_single_generation_on_a_miss()
    {
        $this->assertNull($this->store->get($this->category, $this->request()));
        $this->assertNull($this->store->get($this->category, $this->request()));

        Queue::assertPushed(GenerateCategoryListingSnapshot::class, 1);
        Queue::assertPushed(
            GenerateCategoryListingSnapshot::class,
            fn ($job) => $job->categoryId === 123 && $job->storeId === (int) config('rapidez.store')
        );
    }

    #[Test]
    public function it_queues_again_once_the_lock_is_released()
    {
        $this->store->get($this->category, $this->request());
        $this->store->releaseLock(123);
        $this->store->get($this->category, $this->request());

        Queue::assertPushed(GenerateCategoryListingSnapshot::class, 2);
    }

    #[Test]
    public function it_serves_a_fresh_snapshot()
    {
        $this->store->put($this->category, '<div>snapshot</div>');

        $this->assertEquals('<div>snapshot</div>', $this->store->get($this->category, $this->request()));
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_serves_a_stale_snapshot_while_regenerating()
    {
        config(['rapidez.ssr.ttl' => 60, 'rapidez.ssr.stale_ttl' => 60]);
        $this->store->put($this->category, '<div>snapshot</div>');

        $this->travel(90)->minutes();

        $this->assertEquals('<div>snapshot</div>', $this->store->get($this->category, $this->request()));
        Queue::assertPushed(GenerateCategoryListingSnapshot::class, 1);

        $this->travel(60)->minutes();

        $this->assertNull($this->store->get($this->category, $this->request()));
    }

    #[Test]
    public function it_flushes_the_snapshots_of_a_store()
    {
        $this->store->put($this->category, '<div>snapshot</div>');
        $this->store->flush((int) config('rapidez.store') + 1);

        $this->assertEquals('<div>snapshot</div>', $this->store->get($this->category, $this->request()));

        $this->store->flush((int) config('rapidez.store'));

        $this->assertNull($this->store->get($this->category, $this->request()));
        Queue::assertPushed(GenerateCategoryListingSnapshot::class, 1);

        $this->store->put($this->category, '<div>new snapshot</div>');

        $this->assertEquals('<div>new snapshot</div>', $this->store->get($this->category, $this->request()));
    }

    #[Test]
    public function only_a_full_index_flushes_the_snapshots()
    {
        $this->store->put($this->category, '<div>snapshot</div>');

        (new FlushListingSnapshots)->handle(new IndexStoreAfterEvent(new UpdateIndexCommand, (int) config('rapidez.store')));
        $this->assertEquals('<div>snapshot</div>', $this->store->get($this->category, $this->request()));

        (new FlushListingSnapshots)->handle(new IndexStoreAfterEvent(new IndexCommand, (int) config('rapidez.store')));
        $this->assertNull($this->store->get($this->category, $this->request()));
    }

    #[Test]
    public function it_generates_after_the_response_with_the_sync_queue()
    {
        Bus::fake();
        config(['queue.default' => 'sync']);

        $this->store->get($this->category, $this->request());

        Bus::assertDispatchedAfterResponse(GenerateCategoryListingSnapshot::class);
    }

    #[Test]
    public function it_skips_filtered_urls_by_default()
    {
        $this->store->put($this->category, '<div>snapshot</div>');

        $this->assertNull($this->store->get($this->category, $this->request('?super_color=red')));
        $this->assertNull($this->store->get($this->category, $this->request('?utm_source=newsletter&page=2')));
        $this->assertNull($this->store->get($this->category, $this->request('?hits=24')));
        $this->assertEquals('<div>snapshot</div>', $this->store->get($this->category, $this->request('?utm_source=newsletter&gclid=abc&unknown=1')));
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_serves_a_snapshot_per_filter_combination_when_enabled()
    {
        config(['rapidez.ssr.filters' => true]);
        $this->store->put($this->category, '<div>unfiltered</div>');
        $this->store->put($this->category, '<div>red</div>', ['super_color' => 'red', 'sort' => 'price']);

        $this->assertEquals('<div>red</div>', $this->store->get($this->category, $this->request('?sort=price&utm_source=newsletter&gclid=abc&super_color=red')));
        $this->assertEquals('<div>unfiltered</div>', $this->store->get($this->category, $this->request()));
        Queue::assertNothingPushed();

        $this->assertNull($this->store->get($this->category, $this->request('?super_color=blue&hits=24&gclid=abc')));
        Queue::assertPushed(
            GenerateCategoryListingSnapshot::class,
            fn ($job) => $job->categoryId === 123 && $job->query == ['super_color' => 'blue', 'hits' => '24']
        );
    }

    #[Test]
    public function filter_combinations_have_their_own_lock()
    {
        config(['rapidez.ssr.filters' => true]);

        $this->store->get($this->category, $this->request('?super_color=red'));
        $this->store->get($this->category, $this->request('?super_color=red'));
        $this->store->get($this->category, $this->request('?super_color=blue'));

        Queue::assertPushed(GenerateCategoryListingSnapshot::class, 2);
    }

    #[Test]
    public function it_skips_the_headless_browser_capturing_snapshots()
    {
        $request = $this->request(userAgent: 'Mozilla/5.0 (compatible; ' . GenerateCategoryListingSnapshot::USER_AGENT_TOKEN . '/1.0)');

        $this->assertNull($this->store->get($this->category, $request));
        Queue::assertNothingPushed();
    }

    protected function request(string $query = '', string $userAgent = 'Mozilla/5.0'): Request
    {
        return Request::create('/category.html' . $query, server: ['HTTP_USER_AGENT' => $userAgent]);
    }
}
