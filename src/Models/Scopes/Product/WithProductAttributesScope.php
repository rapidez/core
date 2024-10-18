<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WithProductAttributesScope implements Scope
{
    /** @param Builder<Model> $builder */
    public function apply(Builder $builder, Model $model)
    {
        $builder->addSelect([
            $model->getQualifiedKeyName(),
            $model->qualifyColumn('sku'),
            $model->qualifyColumn('visibility'),
            $model->qualifyColumn('type_id'),
            $model->getQualifiedCreatedAtColumn(),
        ]);

        if (empty($model->attributesToSelect)) {
            return;
        }

        $attributeModel = config('rapidez.models.attribute');
        $attributes = $attributeModel::getCachedWhere(function ($attribute) use ($model) {
            return in_array($attribute['code'], $model->attributesToSelect);
        });

        $attributes = array_filter($attributes, fn ($a) => $a['type'] !== 'static');

        $grammar = $builder->getQuery()->getGrammar();
        foreach ($attributes as $attribute) {
            $attribute = (object) $attribute;

            if ($attribute->flat) {
                if ($attribute->input == 'select' && ! in_array($attribute->source_model, [
                    'Magento\Tax\Model\TaxClass\Source\Product',
                    'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                ])) {
                    $builder->addSelect($builder->qualifyColumn($attribute->code . '_value') . ' AS ' . $attribute->code);
                } else {
                    $builder->addSelect($builder->qualifyColumn($attribute->code) . ' AS ' . $attribute->code);
                }
            } else {
                if ($attribute->input == 'select') {
                    $builder
                        ->selectRaw('COALESCE(ANY_VALUE(' . $grammar->wrap($attribute->code . '_option_value_' . config('rapidez.store') . '.value') . '), ANY_VALUE(' . $grammar->wrap($attribute->code . '_option_value_0.value') . ')) AS ' . $grammar->wrap($attribute->code))
                        ->leftJoin(
                            'catalog_product_entity_' . $attribute->type . ' AS ' . $attribute->code,
                            function ($join) use ($builder, $attribute) {
                                $join->on($attribute->code . '.entity_id', '=', $builder->qualifyColumn('entity_id'))
                                    ->where($attribute->code . '.attribute_id', $attribute->id)
                                    ->where($attribute->code . '.store_id', 0);
                            }
                        )->leftJoin(
                            'eav_attribute_option_value AS ' . $attribute->code . '_option_value_' . config('rapidez.store'),
                            function ($join) use ($attribute) {
                                $join->on($attribute->code . '_option_value_' . config('rapidez.store') . '.option_id', '=', $attribute->code . '.value')
                                    ->where($attribute->code . '_option_value_' . config('rapidez.store') . '.store_id', config('rapidez.store'));
                            }
                        )->leftJoin(
                            'eav_attribute_option_value AS ' . $attribute->code . '_option_value_0',
                            function ($join) use ($attribute) {
                                $join->on($attribute->code . '_option_value_0.option_id', '=', $attribute->code . '.value')
                                    ->where($attribute->code . '_option_value_0.store_id', 0);
                            }
                        );
                } else {
                    $builder
                        ->selectRaw('COALESCE(ANY_VALUE(' . $grammar->wrap($attribute->code . '_' . config('rapidez.store') . '.value') . '), ANY_VALUE(' . $grammar->wrap($attribute->code . '_0.value') . ')) AS ' . $grammar->wrap($attribute->code))
                        ->leftJoin(
                            'catalog_product_entity_' . $attribute->type . ' AS ' . $attribute->code . '_' . config('rapidez.store'),
                            function ($join) use ($builder, $attribute) {
                                $join->on($attribute->code . '_' . config('rapidez.store') . '.entity_id', '=', $builder->qualifyColumn('entity_id'))
                                    ->where($attribute->code . '_' . config('rapidez.store') . '.attribute_id', $attribute->id)
                                    ->where($attribute->code . '_' . config('rapidez.store') . '.store_id', config('rapidez.store'));
                            }
                        )->leftJoin(
                            'catalog_product_entity_' . $attribute->type . ' AS ' . $attribute->code . '_0',
                            function ($join) use ($builder, $attribute) {
                                $join->on($attribute->code . '_0.entity_id', '=', $builder->qualifyColumn('entity_id'))
                                    ->where($attribute->code . '_0.attribute_id', $attribute->id)
                                    ->where($attribute->code . '_0.store_id', 0);
                            }
                        );
                }
            }
        }
    }
}
