<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Rapidez\Core\Models\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class WithProductStockScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->selectRaw('ANY_VALUE(cataloginventory_stock_item.is_in_stock) AS in_stock')
            ->leftJoin('cataloginventory_stock_item', $model->getTable() . '.entity_id', '=', 'cataloginventory_stock_item.product_id');
    }
}
