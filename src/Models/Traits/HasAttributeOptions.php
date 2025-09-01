<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAttributeOptions
{
    protected static $hasAttributeOptions = true;
    protected static $attributeCache = [];

    protected function options(): Attribute
    {
        return Attribute::get(function () {
            $store = config('rapidez.store');
            $key = "store_{$store}.{$this->attribute_code}";

            $value = data_get(static::$attributeCache, $key);
            if ($value) {
                return $value;
            }

            $value = $this->attributeOptions->keyBy('option_id');
            data_set(static::$attributeCache, $key, $value);

            return $value;
        });
    }

    public function attributeOptions(): HasMany
    {
        // Sort by store_id first to always get the higher store id if there are two.
        return $this->hasMany(config('rapidez.models.attribute_option'), 'attribute_id', 'attribute_id')->orderBy('store_id');
    }
}
