<?php

namespace Rapidez\Core\Models\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Searchable as ScoutSearchable;
use TorMorten\Eventy\Facades\Eventy;

trait Searchable
{
    use ScoutSearchable;

    public function toSearchableArray(): array
    {
        $data = $this->searchableData($this->toArray(), $this);

        $modelType = str(static::class)->afterLast('\\')->lower();
        Eventy::filter('index.' . $modelType . '.data', $data, $this);

        // TODO: Maybe double check / handle what attributes are
        // getting indexed here. From makeAllSearchableUsing
        // we're getting the correct data, but when you
        // index just one; I don't think that query
        // will be used resulting in all attrs.

        // Customize the data array...
        // cast to the correct types! (int), etc

        return $data;
    }

    public function searchableData($data, $model): array
    {
        return $data;
    }

    public function shouldBeSearchable(): bool
    {
        return true;
    }

    public function searchableAs(): string
    {
        if (! config('rapidez.index')) {
            throw new Exception('Do not use Scout directly. Please use `php artisan rapidez:index`.');
        }

        return config('rapidez.index');
    }

    protected function makeAllSearchableUsing(Builder $query)
    {
        return $query;
    }

    public function makeSearchableUsing(Collection $models): Collection
    {
        return $models;
    }
}
