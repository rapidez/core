<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;

class QuoteItems implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        // TODO: We're using the super attribute name but this can be overwritten
        // on product level. We're using that in WithProductSuperAttributesScope
        // which joins the catalog_product_super_attribute_label table.
        $attributeModel = config('rapidez.models.attribute');
        $superAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['super'];
        }), 'name', 'id');

        $items = json_decode($value);
        $optionvalueModel = config('rapidez.models.optionvalue');
        foreach ($items as $item) {
            $options = null;
            foreach (json_decode($item->attributes) ?: [] as $attributeId => $attributeValue) {
                $options[$superAttributes[$attributeId]] = is_numeric($attributeValue)
                    ? $optionvalueModel::getCachedByOptionId($attributeValue)
                    : $attributeValue;
            }
            $item->options = $options;
            $configModel = config('rapidez.models.config');
            $item->url = '/'.$item->url_key.$configModel::getCachedByPath('catalog/seo/product_url_suffix', '.html');
            unset($item->attributes);
        }

        return $items;
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
