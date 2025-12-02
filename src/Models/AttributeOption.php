<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class AttributeOption extends Model
{
    protected $table = 'eav_attribute_option';

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('values', function (Builder $builder) {
            $builder->leftJoin('eav_attribute_option_value', function (JoinClause $join) use ($builder) {
                $join->on($builder->qualifyColumn('option_id'), '=', 'eav_attribute_option_value.option_id')
                    ->whereIn('store_id', [0, config('rapidez.store')]);
            });
        });
    }
}
