<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Arr;

class WithProductChildrenScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $flat = $builder->getQuery()->from;
        $attributeModel = config('rapidez.models.attribute');

        $superAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['super'];
        }), 'code');

        $superAttributesSelect = '';
        foreach ($superAttributes as $superAttribute) {
            $superAttributesSelect .= '"'.$superAttribute.'", children.'.$superAttribute.',';
        }

        $builder
            ->selectRaw('IFNULL('.$flat.'.price, MIN(children.price)) as price')
            ->selectRaw('JSON_REMOVE(JSON_OBJECTAGG(IFNULL(children.entity_id, "null__"), JSON_OBJECT(
                "price", children.price,
                '.$superAttributesSelect.'
                "in_stock", stock.is_in_stock,
                "images", (
                    SELECT JSON_ARRAYAGG(catalog_product_entity_media_gallery.value)
                    FROM catalog_product_entity_media_gallery_value_to_entity
                    LEFT JOIN catalog_product_entity_media_gallery ON catalog_product_entity_media_gallery.value_id = catalog_product_entity_media_gallery_value_to_entity.value_id
                    WHERE catalog_product_entity_media_gallery_value_to_entity.entity_id = children.entity_id
                )
            )), "$.null__") AS children')
            ->leftJoin('catalog_product_super_link', 'catalog_product_super_link.parent_id', '=', $flat.'.entity_id')
            ->leftJoin($flat.' AS children', 'children.entity_id', '=', 'catalog_product_super_link.product_id')
            ->leftJoin('cataloginventory_stock_item AS stock', 'children.entity_id', '=', 'stock.product_id');
    }
}
