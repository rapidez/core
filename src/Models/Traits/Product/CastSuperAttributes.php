<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;

trait CastSuperAttributes
{
    protected function getSuperAttributeCasts(): array
    {
        $attributeModel = config('rapidez.models.attribute');
        $superAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['super'];
        }), 'code');

        foreach ($superAttributes as $superAttribute) {
            $casts[$superAttribute] = 'object';
        }

        $casts['super_attributes'] = 'object';

        return $casts;
    }
}
