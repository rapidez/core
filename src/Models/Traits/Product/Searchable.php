<?php

namespace Rapidez\Core\Models\Traits\Product;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Searchable as ScoutSearchable;
use Rapidez\Core\Facades\Rapidez;

trait Searchable
{
    use ScoutSearchable;

    public function toSearchableArray(): array
    {
        $product = $this->toArray();

        // dd($product);

        // TODO: Maybe double check / handle what attributes are
        // getting indexed here. From makeAllSearchableUsing
        // we're getting the correct data, but when you
        // index just one; I don't think that query
        // will be used resulting in all attrs.

        // Customize the data array...
        // cast to the correct types! (int), etc

        return $product;
    }

    public function shouldBeSearchable(): bool
    {
        if (! in_array($this->visibility, config('rapidez.indexer.visibility'))) {
            return false;
        }

        $showOutOfStock = (bool) Rapidez::config('cataloginventory/options/show_out_of_stock', 0);
        if (! $showOutOfStock && ! $this->in_stock) {
            return false;
        }

        return true;
    }

    public function searchableAs(): string
    {
        if (! config('rapidez.index')) {
            throw new Exception('Do not use Scout directly; please use `php artisan rapidez:index`');
        }

        return config('rapidez.index');
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return dd($query
            ->selectOnlyIndexable()
            ->with(['categoryProducts', 'reviewSummary'])
            ->withEventyGlobalScopes('index.product.scopes')
            ->withExists('options AS has_options'));
    }

    public function makeSearchableUsing(Collection $models): Collection
    {
        return $models;
    }
}
