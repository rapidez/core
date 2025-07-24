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
        return Attribute::get(fn () => $this->attributeOptions->keyBy('value_id'));
    }

    public function attributeOptions(): HasMany
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id', 'attribute_id');
    }
}
