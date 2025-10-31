<?php

/*
 * This workaround is currently necessary because `afterQuery` on a relation does not retain custom keys.
 * See also: https://github.com/laravel/framework/issues/57612
 *
 * If this issue is resolved, we can completely remove the trait and the custom relations in favor of `afterQuery`.
 */

namespace Rapidez\Core\Models\Relations;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BelongsToManyCallback extends BelongsToMany
{
    private Closure $callback;

    public function __construct(
        Closure $callback,
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
    )
    {
        $this->callback = $callback;
        parent::__construct($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }

    public function get($columns = ['*'])
    {
        return $this->callback->call($this, parent::get($columns));
    }

    // Override this function to add the keys into the dictionary so this can be eager loaded.
    protected function buildDictionary(Collection $results)
    {
        $dictionary = [];

        foreach ($results as $key => $result) {
            $value = $this->getDictionaryKey($result->{$this->accessor}->{$this->foreignPivotKey});

            $dictionary[$value][$key] = $result;
        }

        return $dictionary;
    }
}
