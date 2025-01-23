<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Support\Arr;
use Rapidez\Core\Casts\Multiselect;

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

        // TODO: If this needs to stay we've to rename the trait and method.
        // Double check if this doesn't effect anything on the frontend!
        // But see the TODO in the boolean.blade.php about the state
        // which is acting weird. When that's working we maybe
        // don't even need this, just check for 1 and 0.
        $booleanAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['input'] == 'boolean';
        }), 'code');

        foreach ($booleanAttributes as $booleanAttribute) {
            $casts[$booleanAttribute] = 'boolean';
        }

        return $casts ?? [];
    }
}
