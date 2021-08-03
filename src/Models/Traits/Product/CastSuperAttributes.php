<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;
use Rapidez\Core\Models\Attribute;

trait CastSuperAttributes
{
    protected function getSuperAttributeCasts(): array
    {
        $superAttributes = Arr::pluck(Attribute::getCachedWhere(function ($attribute) {
            return $attribute['super'];
        }), 'code');

        foreach ($superAttributes as $superAttribute) {
            $casts[$superAttribute] = 'object';
        }

        $casts['super_attributes'] = 'object';

        return $casts;
    }
}
