<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;
use Rapidez\Core\Casts\Multiselect;
use Rapidez\Core\Models\Attribute;

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
