<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Rapidez\Core\Models\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class WithProductSuperAttributesScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $superAttributes = Arr::pluck(Attribute::getCachedWhere(function ($attribute) {
            return $attribute['super'];
        }), 'code', 'id');

        foreach ($superAttributes as $superAttributeId => $superAttribute) {
            $query = DB::table('catalog_product_super_link')
                ->selectRaw('JSON_OBJECTAGG('.$superAttribute.', JSON_OBJECT("text_swatch", '.$superAttribute.'_value, "visual_swatch", eav_attribute_option_swatch.value)) AS '.$superAttribute)
                ->join('catalog_product_flat_1 AS children', 'children.entity_id', '=', 'catalog_product_super_link.product_id')
                ->join('catalog_product_super_attribute', function ($join) use ($superAttributeId) {
                    $join->on('catalog_product_super_attribute.product_id', '=', 'catalog_product_super_link.parent_id')
                         ->where('attribute_id', $superAttributeId);
                })
                ->leftJoin('eav_attribute_option', 'eav_attribute_option.attribute_id', '=', 'catalog_product_super_attribute.attribute_id')
                ->leftJoin('eav_attribute_option_swatch', 'eav_attribute_option_swatch.option_id' , '=', 'children.'.$superAttribute)
                ->whereColumn('parent_id', $model->getTable() . '.entity_id')
                ->whereNotNull($superAttribute);

            $builder->selectSub($query, $superAttribute);
        }

        $query = DB::table('catalog_product_super_attribute')
            ->selectRaw('JSON_OBJECTAGG(eav_attribute.attribute_id, JSON_OBJECT(
                "code", attribute_code,
                "label", COALESCE(NULLIF(catalog_product_super_attribute_label.value, ""), frontend_label),
                "update_image", additional_data->>"$.update_product_preview_image" = 1,
                "swatch_type", additional_data->>"$.swatch_input_type"
            )) AS super_attributes')
            ->join('eav_attribute', 'eav_attribute.attribute_id', '=', 'catalog_product_super_attribute.attribute_id')
            ->join('catalog_eav_attribute', 'catalog_eav_attribute.attribute_id', '=', 'catalog_product_super_attribute.attribute_id')
            ->leftJoin('catalog_product_super_attribute_label', function ($join) {
                $join
                    ->on('catalog_product_super_attribute_label.product_super_attribute_id', '=', 'catalog_product_super_attribute.product_super_attribute_id')
                    ->where('catalog_product_super_attribute_label.store_id', config('rapidez.store'));
            })
            ->whereColumn('product_id', $model->getTable() . '.entity_id')
            ->orderBy('catalog_product_super_attribute.position');

        $builder->selectSub($query, 'super_attributes');
    }
}
