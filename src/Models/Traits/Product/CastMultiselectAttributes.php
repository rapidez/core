<?php

namespace Rapidez\Core\Models\Traits\Product;

use Rapidez\Core\Casts\Multiselect;
use Rapidez\Core\Models\Attribute;
use Illuminate\Support\Arr;

trait CastMultiselectAttributes
{
    protected function getMultiselectAttributeCasts(): array
    {
        $attributeModel = config('rapidez.models.attribute');
        $multiselectAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['input'] == 'multiselect';
        }), 'code');

        foreach ($multiselectAttributes as $multiselectAttribute) {
            $casts[$multiselectAttribute] = Multiselect::class;
        }

        return $casts ?? [];
    }
}
