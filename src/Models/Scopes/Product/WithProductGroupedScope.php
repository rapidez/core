<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use TorMorten\Eventy\Facades\Eventy;

class WithProductGroupedScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $stockQty = config('rapidez.frontend.expose_stock') ? '"qty", grouped_stock.qty,' : '';

        $builder
            ->selectRaw('JSON_REMOVE(JSON_OBJECTAGG(IFNULL(grouped.entity_id, "null__"), JSON_OBJECT(
                ' . Eventy::filter('product.grouped.select', <<<QUERY
                    "id", grouped.entity_id,
                    "sku", grouped.sku,
                    "name", grouped.name,
                    "price", grouped.price,
                    "special_price", grouped.special_price,
                    "special_from_date", DATE(grouped.special_from_date),
                    "special_to_date", DATE(grouped.special_to_date),
                    "in_stock", grouped_stock.is_in_stock,
                    "min_sale_qty", grouped_stock.min_sale_qty,
                    {$stockQty}
                    "qty_increments", IFNULL(NULLIF(grouped_stock.qty_increments, 0), 1)
                QUERY) . '
            )), "$.null__") AS grouped')
            ->leftJoin('catalog_product_link AS grouped_link', function ($join) use ($model) {
                $join->on('grouped_link.product_id', '=', $model->getTable() . '.entity_id')
                     ->where('grouped_link.link_type_id', 3);
            })
            ->leftJoin($model->getTable() . ' as grouped', 'grouped_link.linked_product_id', '=', 'grouped.entity_id')
            ->leftJoin('cataloginventory_stock_item AS grouped_stock', 'grouped.entity_id', '=', 'grouped_stock.product_id');
    }
}
