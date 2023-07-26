<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WithProductTierPriceScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->selectRaw('JSON_OBJECTAGG(
                catalog_product_entity_tier_price.value_id,
                JSON_OBJECT(
                    "group", IF(catalog_product_entity_tier_price.all_groups, -1, catalog_product_entity_tier_price.customer_group_id),
                    "qty", catalog_product_entity_tier_price.qty,
                    "value", catalog_product_entity_tier_price.value,
                    "percentage", catalog_product_entity_tier_price.percentage_value
                )
            ) AS tierprices')
            ->leftJoin('catalog_product_entity_tier_price', function ($join) use ($model) {
                $join->on('catalog_product_entity_tier_price.entity_id', '=', $model->getQualifiedKeyName())
                    ->whereIn('catalog_product_entity_tier_price.website_id', [0, config('rapidez.website')]);
            });
    }
}
