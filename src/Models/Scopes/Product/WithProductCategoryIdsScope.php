<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Rapidez\Core\Models\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class WithProductCategoryIdsScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $query = DB::table('catalog_category_product_index_store' . config('rapidez.store'))
            ->selectRaw('GROUP_CONCAT(category_id)')
            ->whereColumn('product_id', $model->getTable() . '.entity_id');

        $builder->selectSub($query, 'category_ids');
    }
}
