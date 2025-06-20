<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rapidez\Core\Models\Model;

class ProductAttribute extends Model
{
    protected $table = 'catalog_product_entity_varchar';

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('unions', function (Builder $builder) {
            $builder
                ->select([
                    'entity_id',
                    'attribute_id',
                    'store_id',
                    'value',
                ])
                ->whereIn('store_id', [config('rapidez.store'), 0])
                ->whereNotNull('value');

            $baseQuery = clone $builder->getQuery();
            foreach (['int', 'text', 'decimal'] as $type) {
                $typeTable = 'catalog_product_entity_'.$type;
                $typeQuery = (clone $baseQuery)->from($typeTable);
                $typeQuery->wheres[0]['column'] = $typeTable.'.entity_id';
                $builder->unionAll($typeQuery);
            }
        });
    }
}
