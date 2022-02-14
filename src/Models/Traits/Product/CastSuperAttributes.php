<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

trait CastSuperAttributes
{
    protected function getSuperAttributeCasts(): array
    {
        return Cache::driver('array')->rememberForever('super_attributes_'.$this->attributes['id'], function () {
            if($superAttributes = json_decode($this->attributes['super_attributes'], true)){
                $superAttributes = Arr::pluck($superAttributes, 'code');

                foreach ($superAttributes as $superAttribute) {
                    $casts[$superAttribute] = 'object';
                }

                $casts['super_attributes'] = 'object';
            }
            return $casts ?? [];
        });
    }
}
