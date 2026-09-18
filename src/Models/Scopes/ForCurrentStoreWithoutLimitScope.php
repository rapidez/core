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
    use AppliesScopeWithoutLimit;

    public array $uniquePerStoreKeys;

    /**
     * @param  string|array  $uniquePerStoreKey  column(s) that may be duplicate across stores, but must be unique when filtered by store.
     */
    public function __construct(string|array $uniquePerStoreKey, public $storeIdColumn = 'store_id')
    {
        $this->uniquePerStoreKeys = is_array($uniquePerStoreKey) ? $uniquePerStoreKey : [$uniquePerStoreKey];
    }

    public function apply(Builder $query, Model $model)
    {
        return $this->applyScopeWithoutLimit($query, $model, $this->storeIdColumn, $this->uniquePerStoreKeys, config('rapidez.store'));
    }
}
