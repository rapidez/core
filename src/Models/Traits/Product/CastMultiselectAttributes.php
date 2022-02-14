<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Casts\Multiselect;

trait CastMultiselectAttributes
{
    protected function getMultiselectAttributeCasts(): array
    {
        return Cache::driver('array')->rememberForever('multiselect_attributes', function () {
            $attributeModel = config('rapidez.models.attribute');

            $multiselectAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
                return $attribute['input'] == 'multiselect';
            }), 'code');

            foreach ($multiselectAttributes as $multiselectAttribute) {
                $casts[$multiselectAttribute] = Multiselect::class;
            }

            return $casts ?? [];
        });
    }
}
