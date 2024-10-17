<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;
use Rapidez\Core\Casts\Multiselect;

trait CastMultiselectAttributes
{
    /** @return array<string, string> */
    protected function getMultiselectAttributeCasts(): array
    {
        $attributeModel = config('rapidez.models.attribute');

        /** @var array<int, string> */
        $multiselectAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['input'] == 'multiselect';
        }), 'code');

        foreach ($multiselectAttributes as $multiselectAttribute) {
            $casts[$multiselectAttribute] = Multiselect::class;
        }

        return $casts ?? [];
    }
}
