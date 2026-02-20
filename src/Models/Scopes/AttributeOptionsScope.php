<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class AttributeOptionsScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->select([
                'eav_attribute.*',
                'catalog_eav_attribute.*',
                "{$model->getTable()}.*",
                DB::raw('IF(ISNULL(ANY_VALUE(eav_attribute_option_value.value)), null, JSON_ARRAYAGG(eav_attribute_option_value.value)) AS option_values'),
            ])
            ->leftJoin('eav_attribute_option', function (JoinClause $join) use ($model) {
                return $join->on('eav_attribute_option.attribute_id', "{$model->getTable()}.attribute_id")
                    ->where(DB::raw("FIND_IN_SET(eav_attribute_option.option_id, {$model->getTable()}.value)"), '<>', DB::raw(0));
            })
            ->leftJoin('eav_attribute_option_value', 'eav_attribute_option_value.option_id', 'eav_attribute_option.option_id')
            ->groupBy("{$model->getTable()}.value_id");
    }
}
