<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;

class SuperAttribute extends Model
{
    protected $table = 'catalog_product_super_attribute';
    protected $primaryKey = 'product_super_attribute_id';

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('attribute', function (Builder $builder) {
            $builder->leftJoin('eav_attribute', $builder->qualifyColumn('attribute_id'), '=', 'eav_attribute.attribute_id');
        });
    }
}
