<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\Product\Eav\EavDatetime;
use Rapidez\Core\Models\Product\Eav\EavDecimal;
use Rapidez\Core\Models\Product\Eav\EavInt;
use Rapidez\Core\Models\Product\Eav\EavText;
use Rapidez\Core\Models\Product\Eav\EavVarchar;

/**
 * @property Collection $eav_values
 */
trait HasEavAttributes
{
    public function scopeWithCustomFields($q, ?callable $cb = null)
    {
        $cb = $cb ?: fn ($q) => null;

        return $q->with([
            'varchar.attribute' => $cb,
            'text.attribute'    => $cb,
            'int.attribute'     => $cb,
            'int.optionValue',
            'decimal.attribute'  => $cb,
            'datetime.attribute' => $cb,
        ]);
    }

    public function scopeWhereValueHas($q, $cb)
    {
        return $q->whereHas('varchar', $cb)
            ->orWhereHas('int', $cb)
            ->orWhereHas('decimal', $cb)
            ->orWhereHas('datetime', $cb)
            ->orWhereHas('text', $cb);
    }

    public function scopeOrderByEavValue($q, $direction = 'desc', ?callable $cb = null)
    {
        $cb = $cb ?: fn ($q) => null;

        return $q->withAggregate(
            [
                'varchar'  => $cb,
                'text'     => $cb,
                'int'      => $cb,
                'decimal'  => $cb,
                'datetime' => $cb,
            ],
            'value'
        )
            ->orderBy('custom_field_varchar_value', $direction)
            ->orderBy('custom_field_text_value', $direction)
            ->orderBy('custom_field_int_value', $direction)
            ->orderBy('custom_field_decimal_value', $direction)
            ->orderBy('custom_field_datetime_value', $direction);
    }

    public function varchar(): HasMany
    {
        return $this->hasMany(EavVarchar::class, 'entity_id', 'entity_id');
    }

    public function text(): HasMany
    {
        return $this->hasMany(EavText::class, 'entity_id', 'entity_id');
    }

    public function int(): HasMany
    {
        return $this->hasMany(EavInt::class, 'entity_id', 'entity_id');
    }

    public function decimal(): HasMany
    {
        return $this->hasMany(EavDecimal::class, 'entity_id', 'entity_id');
    }

    public function datetime(): HasMany
    {
        return $this->hasMany(EavDatetime::class, 'entity_id', 'entity_id');
    }

    public function eavValues(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing([
                    'varchar.attribute',
                    'text.attribute',
                    'int.attribute',
                    'int.optionValue',
                    'decimal.attribute',
                    'datetime.attribute',
                ]);

                return $this->varchar
                    ->concat($this->text)
                    ->concat($this->int)
                    ->concat($this->decimal)
                    ->concat($this->datetime)
                    ->keyBy('attribute.attribute_code');
            },
        );
    }
}
