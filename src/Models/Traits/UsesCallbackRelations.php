<?php

/*
 * This workaround is currently necessary because `afterQuery` on a relation does not retain custom keys.
 * See also: https://github.com/laravel/framework/issues/57612
 *
 * If this issue is resolved, we can completely remove the trait and the custom relations in favor of `afterQuery`.
 */

namespace Rapidez\Core\Models\Traits;

use Closure;
use Rapidez\Core\Models\Relations\BelongsToManyCallback;
use Rapidez\Core\Models\Relations\HasManyCallback as HasManyCallbackRelation;

trait UsesCallbackRelations
{
    protected function hasManyCallback(Closure $callback, $related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasManyCallbackRelation($callback, $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    protected function belongsToManyCallback(
        Closure $callback,
        $related,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null,
    ) {
        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related, $instance);
        }

        return new BelongsToManyCallback(
            $callback,
            $instance->newQuery(),
            $this,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(),
            $relation,
        );
    }
}
