<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;

trait CastSuperAttributes
{
    protected function getSuperAttributeCasts(): array
    {
        $attributeModel = config('rapidez.models.attribute');
        $superAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['super'] && $attribute['flat'];
        }), 'code');

        foreach ($superAttributes as $superAttribute) {
            $casts['super_' . $superAttribute] = 'object';
        }

        $casts['super_attributes'] = 'object';

        return $casts;
    }
}
