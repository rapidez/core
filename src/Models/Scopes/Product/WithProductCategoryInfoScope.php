<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WithProductCategoryInfoScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // We're joining this on everything, also the simple product so we
        // remove the duplicates with the distinct, maybe it's a better
        // idea to create a relation and eager load this? Previously
        // with the subselect this wasn't an issue.
        $builder
            ->selectRaw('GROUP_CONCAT(DISTINCT(category_id)) as category_ids')
            ->selectRaw('GROUP_CONCAT(DISTINCT(catalog_category_flat_store_'.config('rapidez.store').'.path)) as category_paths')
            ->leftJoin('catalog_category_product_index_store'.config('rapidez.store'), 'catalog_category_product_index_store'.config('rapidez.store').'.product_id', '=', $model->getTable().'.entity_id')
            ->leftJoin('catalog_category_flat_store_'.config('rapidez.store'), 'catalog_category_flat_store_'.config('rapidez.store').'.entity_id', '=', 'catalog_category_product_index_store'.config('rapidez.store').'.category_id');
    }
}
