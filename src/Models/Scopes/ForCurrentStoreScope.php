<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class ForCurrentStoreScope implements Scope
{
    public ?string $joinTable;

    public function __construct($joinTable = null)
    {
        $this->joinTable = $joinTable;
    }

    public function apply(Builder $builder, Model $model)
    {
        $currentTable = $builder->getQuery()->from;
        $joinTable = $this->joinTable ?: $currentTable . '_store';

        $storeQuery = DB::table($joinTable)
            ->whereIn('store_id', [0, config('rapidez.store')])
            ->orderByDesc('store_id')
            ->limit(1);

        $builder->leftJoinLateral($storeQuery, $joinTable);
    }
}
