<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Rapidez\Core\Models\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class WithProductEssentialAttributesScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $attributes = Attribute::getCachedWhere(function ($attribute) {
            return in_array($attribute['code'], ['visibility']);
        });

        foreach ($attributes as $attribute) {
            $attribute = (object)$attribute;
            $builder
                ->selectRaw('COALESCE(ANY_VALUE('.$attribute->code.'_'.config('rapidez.store').'.value), ANY_VALUE('.$attribute->code.'_0.value)) AS '.$attribute->code)
                ->leftJoin(
                    'catalog_product_entity_'.$attribute->type.' AS '.$attribute->code.'_'.config('rapidez.store'),
                    function ($join) use ($builder, $attribute) {
                        $join->on($attribute->code.'_'.config('rapidez.store').'.entity_id', '=', $builder->getQuery()->from.'.entity_id')
                             ->where($attribute->code.'_'.config('rapidez.store').'.attribute_id', $attribute->id)
                             ->where($attribute->code.'_'.config('rapidez.store').'.store_id', config('rapidez.store'));
                    }
                )->leftJoin(
                    'catalog_product_entity_'.$attribute->type.' AS '.$attribute->code.'_0',
                    function ($join) use ($builder, $attribute) {
                        $join->on($attribute->code.'_0.entity_id', '=', $builder->getQuery()->from.'.entity_id')
                             ->where($attribute->code.'_0.attribute_id', $attribute->id)
                             ->where($attribute->code.'_0.store_id', 0);
                    }
                );
        }
    }
}
