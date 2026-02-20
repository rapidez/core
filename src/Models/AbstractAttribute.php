<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Rapidez\Core\Models\Scopes\AttributeOptionsScope;
use Rapidez\Core\Models\Scopes\ForCurrentStoreWithoutLimitScope;

class AbstractAttribute extends Model
{
    protected $casts = [
        'option_values' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('store', new ForCurrentStoreWithoutLimitScope(['attribute_id', 'entity_id']));

        static::addGlobalScope('attribute', function (Builder $builder) {
            $builder->leftJoin('eav_attribute', function (JoinClause $join) use ($builder) {
                $join->on($builder->qualifyColumn('attribute_id'), '=', 'eav_attribute.attribute_id')
                    ->when(
                        $builder->getModel()->entity_type_id,
                        fn ($query) => $query->where('entity_type_id', $builder->getModel()->entity_type_id)
                    );
            });
        });

        if (isset(static::$hasAttributeOptions)) { // @phpstan-ignore-line
            static::addGlobalScope(new AttributeOptionsScope);
        }
    }

    public function getTable()
    {
        // Overwritten to always return table, so that qualifyColumn and such work
        return $this->table;
    }

    protected function rawValue(): Attribute
    {
        return Attribute::get(function ($value) {
            $value ??= $this->getAttributeFromArray('value');
            $class = config('rapidez.attribute-models')[$this->backend_model] ?? null;

            if ($class) {
                return $class::value($value, $this);
            }

            return array_key_exists('value', $this->getCasts())
                ? $this->castAttribute('value', $value)
                : $value;
        });
    }

    protected function value(): Attribute
    {
        return Attribute::get(function ($value) {
            return $this->option_values ?? $this->rawValue ?? $value;
        });
    }

    protected function label(): Attribute
    {
        return Attribute::get(function () {
            return is_iterable($this->value)
                ? implode(', ', iterable_to_array($this->value))
                : $this->value;
        });
    }

    protected function sortOrder(): Attribute
    {
        return Attribute::get(fn () => $this->options[$this->rawValue]->sort_order ?? null);
    }

    public function __toString(): string
    {
        return (string) $this->label;
    }

    public function toArray()
    {
        return $this->value;
    }
}
