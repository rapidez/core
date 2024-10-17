<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use TorMorten\Eventy\Facades\Eventy;

trait SelectAttributeScopes
{
    /**
     * @param  Builder<Model>  $query
     * @param  array<int, string>  $attributes
     * @return Builder<Model>
     * */
    public function scopeSelectAttributes(Builder $query, array $attributes): Builder
    {
        $this->attributesToSelect = $attributes;

        return $query;
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     * */
    public function scopeSelectForProductPage(Builder $query): Builder
    {
        $attributeModel = config('rapidez.models.attribute');
        $this->attributesToSelect = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['productpage'] || in_array($attribute['code'], [
                'name',
                'meta_title',
                'meta_description',
                'price',
                'special_price',
                'special_from_date',
                'special_to_date',
                'description',
                'url_key',
            ]);
        }), 'code');

        return $query;
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     * */
    public function scopeSelectOnlyComparable(Builder $query): Builder
    {
        $attributeModel = config('rapidez.models.attribute');
        $this->attributesToSelect = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['compare'] || in_array($attribute['code'], ['name']);
        }), 'code');

        return $query;
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     * */
    public function scopeSelectOnlyIndexable(Builder $query): Builder
    {
        $attributeModel = config('rapidez.models.attribute');
        $this->attributesToSelect = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            if (in_array($attribute['code'], ['msrp_display_actual_price_type', 'price_view', 'shipment_type', 'status'])) {
                return false;
            }

            if ($attribute['listing'] || $attribute['filter']) {
                return true;
            }

            // @phpstan-ignore-next-line
            $alwaysInFlat = array_merge(['sku'], Eventy::filter('index.product.attributes', []));
            if (in_array($attribute['code'], $alwaysInFlat)) {
                return true;
            }

            return false;
        }), 'code');

        return $query;
    }
}
