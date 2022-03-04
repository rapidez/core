<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;
use Rapidez\Core\Casts\SortedSuperAttributes;

trait CastSuperAttributes
{
    protected function getSuperAttributeCasts(): array
    {
        $attributeModel = config('rapidez.models.attribute');
        $superAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['super'];
        }), 'code');

        foreach ($superAttributes as $superAttribute) {
            $casts[$superAttribute] = SortedSuperAttributes::class;
        }

        $casts['super_attributes'] = 'object';

        return $casts;
    }
}
