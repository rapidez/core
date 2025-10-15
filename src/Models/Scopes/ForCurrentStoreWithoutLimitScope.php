<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

/**
 * Remove results from the default store when store view specific is found.
 */
class ForCurrentStoreWithoutLimitScope implements Scope
{
    public array $uniquePerStoreKeys;

    public function __construct(string|array $uniquePerStoreKey, public $storeIdColumn = 'store_id')
    {
        $this->uniquePerStoreKeys = is_array($uniquePerStoreKey) ? $uniquePerStoreKey : [$uniquePerStoreKey];
    }

    public function apply(Builder $query, Model $model)
    {
        if (! config('rapidez.store')) {
            return $query
                ->where($query->qualifyColumn($this->storeIdColumn), 0);
        }

        $scope = $this;

        return $query
            // Pre-filter results to be default and current store only.
            ->whereIn($query->qualifyColumn($this->storeIdColumn), [0, config('rapidez.store')])
            // Remove values from the default store where values for the current store exist.
            ->where(fn ($query) => $query
                // Remove values where we already have values in the current store.
                ->where(function ($query) use ($scope, $model) {
                    $query
                        ->whereNotExists(function ($query) use ($scope, $model) {
                            $query
                                ->select(DB::raw(1))
                                ->from($model->getTable() . ' as comparison')
                                ->where('comparison.' . $this->storeIdColumn, config('rapidez.store'));
                            foreach ($scope->uniquePerStoreKeys as $uniquePerStoreKey) {
                                $query->whereColumn('comparison.' . $uniquePerStoreKey, $model->qualifyColumn($uniquePerStoreKey));
                            }
                        });
                })
                // Unless the value IS the current store.
                ->orWhere($query->qualifyColumn($this->storeIdColumn), config('rapidez.store'))
            );
    }
}
