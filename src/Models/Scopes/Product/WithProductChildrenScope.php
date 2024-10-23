<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Arr;
use TorMorten\Eventy\Facades\Eventy;

class WithProductChildrenScope implements Scope
{
    /** @param Builder<Model> $builder */
    public function apply(Builder $builder, Model $model)
    {
        /** @var string $flat */
        $flat = $builder->getQuery()->from;
        $attributeModel = config('rapidez.models.attribute');

        $superAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['super'] && $attribute['flat'];
        }), 'code');

        $grammar = $builder->getQuery()->getGrammar();
        $superAttributesSelect = '';
        foreach ($superAttributes as $superAttribute) {
            $superAttributesSelect .= '"' . $superAttribute . '", ' . $grammar->wrap('children.' . $superAttribute) . ',';
        }

        $store = config('rapidez.store', 0);
        $stockQty = config('rapidez.system.expose_stock') ? '"qty", children_stock.qty,' : '';

        $selects = Eventy::filter('product.children.select', <<<QUERY
            "sku", children.sku,
            "price", children.price,
            "special_price", children.special_price,
            "special_from_date", DATE(children.special_from_date),
            "special_to_date", DATE(children.special_to_date),
            {$superAttributesSelect}
            "in_stock", children_stock.is_in_stock,
            {$stockQty}
            "images", (
                SELECT JSON_ARRAYAGG(JSON_OBJECT("value", catalog_product_entity_media_gallery.value, "position", catalog_product_entity_media_gallery_value.position))
                FROM catalog_product_entity_media_gallery_value
                LEFT JOIN catalog_product_entity_media_gallery ON catalog_product_entity_media_gallery.value_id = catalog_product_entity_media_gallery_value.value_id
                WHERE catalog_product_entity_media_gallery_value.entity_id = children.entity_id
                AND catalog_product_entity_media_gallery_value.disabled = 0
                AND catalog_product_entity_media_gallery_value.store_id IN (0, {$store})
                AND (
                    catalog_product_entity_media_gallery_value.value_id not in (
                        SELECT v2.value_id FROM catalog_product_entity_media_gallery_value as v2 WHERE catalog_product_entity_media_gallery_value.value_id = v2.value_id AND v2.store_id = {$store}
                    )
                    OR catalog_product_entity_media_gallery_value.store_id = {$store}
                )
            )
        QUERY);

        $builder
            ->selectRaw('JSON_REMOVE(JSON_OBJECTAGG(IFNULL(children.entity_id, "null__"), JSON_OBJECT(' . $selects . ')), "$.null__") AS children')
            ->leftJoin('catalog_product_super_link', 'catalog_product_super_link.parent_id', '=', $flat . '.entity_id')
            ->leftJoin($flat . ' AS children', 'children.entity_id', '=', 'catalog_product_super_link.product_id')
            ->leftJoin('cataloginventory_stock_item AS children_stock', 'children.entity_id', '=', 'children_stock.product_id');
    }
}
