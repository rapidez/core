<?php

namespace Rapidez\Core\Models\Product\Eav\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Rapidez\Core\Models\Product\Eav\EavOptionValue;
use Rapidez\Core\Models\Product\Eav\FindInSet\FindInSetMany;

trait HasMultiselect
{
    public static function bootHasMultiselect()
    {
        static::addGlobalScope('withOptionValues', function ($query) {
            $query->with('optionValues');
        });
    }

    public function optionValues(): FindInSetMany
    {
        return $this->findInSetMany(EavOptionValue::class, 'value', 'option_id');
    }

    public function findInSetMany($related, $localKey = null, $foreignKey = null, $index = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new FindInSetMany(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey, $index
        );
    }

    public function value(): Attribute
    {
        return Attribute::make(
            function ($value) {
                if ($this->relationLoaded('optionValues') && in_array($this->attribute->frontend_input, ['select', 'multiselect']) && $this->attribute->source_model === null) {
                    if ($this->optionValues?->isEmpty()) {
                        return $value;
                    }
                    return $this->optionValues->map?->value ?: $value;
                }

                return $value;
            }
        );
    }
}
