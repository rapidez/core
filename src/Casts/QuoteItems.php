<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\Config;
use Rapidez\Core\Models\OptionValue;

class QuoteItems implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        // TODO: We're using the super attribute name but this can be overwritten
        // on product level. We're using that in WithProductSuperAttributesScope
        // which joins the catalog_product_super_attribute_label table.
        $superAttributes = Arr::pluck(Attribute::getCachedWhere(function ($attribute) {
            return $attribute['super'];
        }), 'name', 'id');

        $items = json_decode($value);

        foreach ($items as $item) {
            $options = null;
            foreach (json_decode($item->attributes) ?: [] as $attributeId => $attributeValue) {
                $options[$superAttributes[$attributeId]] = OptionValue::getCachedByOptionId($attributeValue);
            }
            $item->options = $options;
            $item->url = '/'.$item->url_key.Config::getCachedByPath('catalog/seo/product_url_suffix', '.html');
            unset($item->attributes);
        }

        return $items;
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
