<?php

namespace Rapidez\Core\Models\Traits;

use Exception;
use Laravel\Scout\Searchable as ScoutSearchable;
use TorMorten\Eventy\Facades\Eventy;

trait Searchable
{
    use ScoutSearchable;

    /**
     * {@inheritdoc}
     */
    public function toSearchableArray(): array
    {
        // TODO: Maybe use the model name here?
        return Eventy::filter('index.' . config('rapidez.index') . '.data', $this->toArray(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function searchableAs(): string
    {
        if (! config('rapidez.index')) {
            throw new Exception('Do not use Scout directly. Please use `php artisan rapidez:index`.');
        }

        return implode('_', array_values([
            config('scout.prefix'),
            // TODO: Maybe use the model name here?
            config('rapidez.index'),
            config('rapidez.store'),
        ]));
    }
}
