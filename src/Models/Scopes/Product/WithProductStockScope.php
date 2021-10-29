<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WithProductStockScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->selectRaw('ANY_VALUE(cataloginventory_stock_item.is_in_stock) AS in_stock')
            ->selectRaw('IF(ANY_VALUE(cataloginventory_stock_item.enable_qty_increments), ANY_VALUE(cataloginventory_stock_item.qty_increments), 1) AS qty_increments')
            ->selectRaw('IF(ANY_VALUE(cataloginventory_stock_item.qty), ANY_VALUE(cataloginventory_stock_item.qty), 1) AS stock_qty')
            ->leftJoin('cataloginventory_stock_item', $model->getTable().'.entity_id', '=', 'cataloginventory_stock_item.product_id');
    }
}
