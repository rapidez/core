<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Rapidez\Core\Models\Scopes\ForCurrentStoreWithoutLimitScope;

class EavAttribute extends Model
{
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new ForCurrentStoreWithoutLimitScope('value_id'));

        static::addGlobalScope('attribute', function (Builder $builder) {
            $builder->leftJoin('eav_attribute', $builder->qualifyColumn('attribute_id'), '=', 'eav_attribute.attribute_id');
        });

        if (isset(static::$hasAttributeOptions)) {
            static::addGlobalScope('attributeOptions', function (Builder $builder) {
                $builder->with('attributeOptions');
            });
        }
    }

    public function getTable()
    {
        // Overwritten to always return table, so that qualifyColumn and such work
        return $this->table;
    }

    protected function value(): Attribute
    {
        return Attribute::get(function ($value) {
            if ($this->frontend_input === 'select') {
                return $this->options[$value]?->value ?? $value;
            }

            $class = config('rapidez.attribute-models')[$this->backend_model] ?? null;
            if ($class) {
                return $class::value($value, $this);
            }

            return $value;
        });
    }
}
