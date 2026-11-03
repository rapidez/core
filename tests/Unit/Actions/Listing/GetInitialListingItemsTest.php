<?php

namespace Rapidez\Core\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Rapidez\Core\Actions\Listing\GetInitialListingItems;
use Rapidez\Core\Tests\TestCase;
use RuntimeException;

class GetInitialListingItemsTest extends TestCase
{
    #[Test]
    public function for_values_returns_empty_array_without_searching_when_no_values_given()
    {
        $action = new GetInitialListingItems;

        $this->assertSame([], $action->forValues('sku', []));
    }

    #[Test]
    public function it_fails_soft_when_the_search_engine_throws()
    {
        config(['rapidez.models.product' => ThrowingSearchableProductStub::class]);

        $action = new GetInitialListingItems;

        $this->assertSame([], $action->forCategory(5));
        $this->assertSame([], $action->forSearch('jacket'));
        $this->assertSame([], $action->forValues('sku', ['ABC']));
    }
}

class ThrowingSearchableProductStub
{
    public static function search(string $query)
    {
        throw new RuntimeException('Simulated search engine failure.');
    }
}
