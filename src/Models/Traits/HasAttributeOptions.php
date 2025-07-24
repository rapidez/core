<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\AttributeOption;

trait HasAttributeOptions
{
    protected static $hasAttributeOptions = true;

    protected function options(): Attribute
    {
        // Sort by store_id first to always get the higher store id if there are two.
        return Attribute::get(fn () =>
            $this->attributeOptions
                ->sortBy('store_id')
                ->keyBy('option_id')
        );
    }

    public function attributeOptions(): HasMany
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id', 'attribute_id');
    }
}
