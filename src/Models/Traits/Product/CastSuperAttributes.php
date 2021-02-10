<?php

namespace Rapidez\Core\Models\Traits\Product;

use Rapidez\Core\Models\Attribute;
use Illuminate\Support\Arr;

trait CastSuperAttributes
{
    protected function getSuperAttributeCasts(): array
    {
        $superAttributes = Arr::pluck(Attribute::getCachedWhere(function ($attribute) {
            return $attribute['super'];
        }), 'code');

        foreach ($superAttributes as $superAttribute) {
            $casts['super_'.$superAttribute] = 'object';
        }

        $casts['super_attributes'] = 'object';

        return $casts;
    }
}
