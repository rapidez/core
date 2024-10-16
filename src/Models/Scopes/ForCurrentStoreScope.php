<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class ForCurrentStoreScope implements Scope
{
    public ?string $joinTable;

    public function __construct(?string $joinTable = null)
    {
        $this->joinTable = $joinTable;
    }

    /** @param Builder<Model> $builder */
    public function apply(Builder $builder, Model $model)
    {
        $currentTable = $builder->getQuery()->from;
        if (!is_string($currentTable)) {
            return;
        }

        $joinTable = $this->joinTable ?: $currentTable . '_store';
        $primaryKey = $model->getKeyName();

        $storeQuery = DB::table($joinTable)
            ->where($builder->qualifyColumn($primaryKey), DB::raw($joinTable . '.' . $primaryKey))
            ->whereIn('store_id', [0, config('rapidez.store')])
            ->orderByDesc('store_id')
            ->limit(1);

        $builder->joinLateral($storeQuery, $joinTable);
    }
}
