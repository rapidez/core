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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyCallback extends HasMany
{
    private Closure $callback;

    public function __construct(Closure $callback, Builder $query, Model $parent, string $foreignKey, string $localKey)
    {
        $this->callback = $callback;
        parent::__construct($query, $parent, $foreignKey, $localKey);
    }

    public function getResults()
    {
        return $this->callback->call($this, parent::getResults());
    }

    protected function getRelationValue(array $dictionary, $key, $type)
    {
        return $this->callback->call($this, parent::getRelationValue($dictionary, $key, $type));
    }
}
