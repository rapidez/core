<?php

namespace Rapidez\Core\Models\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable as ScoutSearchable;
use TorMorten\Eventy\Facades\Eventy;

trait Searchable
{
    use ScoutSearchable;

    public ?string $indexName;

    public function toSearchableArray(): array
    {
        $data = $this->searchableData($this->toArray(), $this);

        // TODO: Is this filter still useful? Overriding the model gives you full control
        return Eventy::filter('index.' . $this->getIndexName() . '.data', $data, $this);
    }

    // TODO: Not sure if this is the best idea, we already
    // have the toSearchableArray for this.
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

    public function getIndexName(): string
    {
        return $this->indexName ?? Str::snake(Str::pluralStudly(class_basename(static::class)));
    }

    public function searchableAs(): string
    {

        return implode('_', array_values([
            config('scout.prefix'),
            $this->getIndexName(),
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
