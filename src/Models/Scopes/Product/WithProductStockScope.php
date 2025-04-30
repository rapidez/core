<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Rapidez\Core\Facades\Rapidez;

class WithProductStockScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (config('rapidez.system.expose_stock')) {
            $builder->selectRaw('ANY_VALUE(cataloginventory_stock_item.qty) AS qty');
        }

        $configBackorder = Rapidez::config('cataloginventory/item_options/backorders');

        // TODO: These values should listen to:
        // - use_config_min_sale_qty
        // - use_config_max_sale_qty
        // - use_config_enable_qty_inc
        // - use_config_qty_increments

        $builder
            ->selectRaw('ANY_VALUE(IF(cataloginventory_stock_item.use_config_backorders, ' . ($configBackorder ?: '0') . ', cataloginventory_stock_item.backorders)) as backorder_type')
            ->selectRaw('ANY_VALUE(cataloginventory_stock_item.manage_stock) as manage_stock')
            ->selectRaw('ANY_VALUE(cataloginventory_stock_item.min_sale_qty) as min_sale_qty')
            ->selectRaw('ANY_VALUE(cataloginventory_stock_item.max_sale_qty) as max_sale_qty')
            ->selectRaw('ANY_VALUE(cataloginventory_stock_item.is_in_stock) AS in_stock')
            ->selectRaw('IF(ANY_VALUE(cataloginventory_stock_item.enable_qty_increments), ANY_VALUE(cataloginventory_stock_item.qty_increments), 1) AS qty_increments')
            ->leftJoin('cataloginventory_stock_item', $model->getTable() . '.entity_id', '=', 'cataloginventory_stock_item.product_id');
    }
}
