<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Remove results from the default store when store view specific is found.
 */
class ForCurrentStoreWithoutLimitScope implements Scope
{
    public function __construct(public $uniquePerStoreKey, public $storeIdColumn = 'store_id')
    {
    }

    public function apply(Builder $query, Model $model)
    {
        if (! config('rapidez.store')) {
            return $query
                ->where($query->qualifyColumn($this->storeIdColumn), 0);
        }

        return $query
            // Pre-filter results to be default and current store only.
            ->whereIn($query->qualifyColumn($this->storeIdColumn), [0, config('rapidez.store')])
            // Remove values from the default store where values for the current store exist.
            ->where(fn ($query) => $query
                // Remove values where we already have values in the current store.
                ->whereNotIn($query->qualifyColumn($this->uniquePerStoreKey), fn ($query) => $query
                    ->from($model->getTable())
                    ->select($model->qualifyColumn($this->uniquePerStoreKey))
                    ->whereColumn($model->qualifyColumn($this->uniquePerStoreKey), $model->qualifyColumn($this->uniquePerStoreKey))
                    ->where($model->qualifyColumn($this->storeIdColumn), config('rapidez.store'))
                )
                // Unless the value IS the current store.
                ->orWhere($query->qualifyColumn($this->storeIdColumn), config('rapidez.store'))
            );
    }
}
