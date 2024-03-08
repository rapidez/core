<?php

namespace Rapidez\Core\Models\Scopes\Attribute;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class OnlyProductAttributesScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->select(
                'eav_attribute.attribute_id as id',
                DB::raw('COALESCE(eav_attribute_label.value, frontend_label, eav_attribute.attribute_code) AS `name`'),
                'eav_attribute.attribute_code AS code',
                'backend_type AS type',
                'frontend_input AS input',
                'source_model',
                DB::raw('IF(eav_attribute.attribute_code IN ("price", "tax_class_id"), 0, is_searchable) AS `search`'),
                'search_weight',
                'is_filterable AS filter',
                'is_comparable AS compare',
                'used_in_product_listing AS listing',
                'used_for_sort_by AS sorting',
                'is_visible_on_front AS productpage',
                'is_html_allowed_on_front AS html',
                DB::raw('IF(source_model NOT LIKE "Magento%", 0, GREATEST(
                        IF(backend_type = "static", 1, 0),
                        is_used_for_promo_rules,
                        used_in_product_listing,
                        used_for_sort_by,
                        IF(eav_attribute.attribute_code IN ("status", "required_options", "tax_class_id", "weight"), 1, 0)
                    )) AS `flat`'),
                DB::raw('EXISTS(
                        SELECT 1
                        FROM catalog_product_super_attribute
                        WHERE catalog_product_super_attribute.attribute_id = eav_attribute.attribute_id
                    ) AS `super`'),
                DB::raw('additional_data->>"$.swatch_input_type" = "text" AS `text_swatch`'),
                DB::raw('additional_data->>"$.swatch_input_type" = "visual" AS `visual_swatch`'),
                DB::raw('additional_data->>"$.update_product_preview_image" = 1 AS `update_image`'),
                'position'
            )
            ->join('catalog_eav_attribute', 'eav_attribute.attribute_id', '=', 'catalog_eav_attribute.attribute_id')
            ->leftJoin('eav_attribute_label', function ($join) {
                $join->on('eav_attribute.attribute_id', '=', 'eav_attribute_label.attribute_id')
                    ->where('eav_attribute_label.store_id', config('rapidez.store'));
            })
            ->where('entity_type_id', 4); // catalog_product
    }
}
