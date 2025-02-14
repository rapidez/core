<?php

namespace Rapidez\Core\Models\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable as ScoutSearchable;
use TorMorten\Eventy\Facades\Eventy;

trait Searchable
{
    use ScoutSearchable;

    public function toSearchableArray(): array
    {
        $data = $this->searchableData($this->toArray(), $this);

        return Eventy::filter('index.' . config('rapidez.index') . '.data', $data, $this);
    }

    public function searchableData(array $data, Model $model): array
    {
        // Customize the data with this function.
        // - $data is the actual array with data.
        // - $model is useful for when you want to retrieve data from the actual model (e.g. relations).
        // Return the array with data that you want to send to ES.

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

        return implode('_', array_values([
            config('scout.prefix'),
            config('rapidez.index'),
            config('rapidez.store'),
        ]));
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
