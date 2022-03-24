<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;
use TorMorten\Eventy\Facades\Eventy;

class WithProductGroupedScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->selectRaw('JSON_REMOVE(JSON_OBJECTAGG(IFNULL(grouped.entity_id, "null__"), JSON_OBJECT(
                '.Eventy::filter('product.grouped.select', <<<QUERY
                    "id", grouped.entity_id,
                    "sku", grouped.sku,
                    "name", grouped.name,
                    "price", grouped.price,
                    "special_price", grouped.special_price,
                    "special_from_date", DATE(grouped.special_from_date),
                    "special_to_date", DATE(grouped.special_to_date),
                    "in_stock", grouped_stock.is_in_stock,
                    "qty_increments", IFNULL(NULLIF(grouped_stock.qty_increments, 0), 1)
                QUERY).'
            )), "$.null__") AS grouped')
            ->leftJoin('catalog_product_link', function ($join) use ($model) {
                $join->on('catalog_product_link.product_id', '=', $model->getTable().'.entity_id')
                     ->where('link_type_id', 3);
            })
            ->leftJoin($model->getTable().' as grouped', 'linked_product_id', '=', 'grouped.entity_id')
            ->leftJoin('cataloginventory_stock_item AS grouped_stock', 'grouped.entity_id', '=', 'grouped_stock.product_id');
    }
}
