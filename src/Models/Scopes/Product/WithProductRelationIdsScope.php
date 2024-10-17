<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class WithProductRelationIdsScope implements Scope
{
    /** @param Builder<Model> $builder */
    public function apply(Builder $builder, Model $model)
    {
        foreach ([1 => 'relation', 4 => 'upsell'] as $linkTypeId => $linkCode) {
            $query = DB::table('catalog_product_link')
                ->selectRaw('GROUP_CONCAT(linked_product_id)')
                ->where('link_type_id', $linkTypeId)
                ->whereColumn('product_id', $model->getTable() . '.entity_id');

            $builder->selectSub($query, $linkCode . '_ids');
        }
    }
}
