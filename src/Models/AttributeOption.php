<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;

class AttributeOption extends Model
{
    protected $table = 'eav_attribute_option';

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('values', function (Builder $builder) {
            $builder->leftJoin('eav_attribute_option_value', $builder->qualifyColumn('option_id'), '=', 'eav_attribute_option_value.option_id');
        });
    }
}
