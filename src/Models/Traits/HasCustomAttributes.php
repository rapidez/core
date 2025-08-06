<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as AttributeCast;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\AttributeDatetime;
use Rapidez\Core\Models\AttributeDecimal;
use Rapidez\Core\Models\AttributeInt;
use Rapidez\Core\Models\AttributeText;
use Rapidez\Core\Models\AttributeVarchar;

trait HasCustomAttributes
{
    public function scopeWithCustomAttributes(Builder $builder, ?callable $callback = null)
    {
        if ($callback) {
            $builder->with([
                'attributeDatetime' => $callback,
                'attributeDecimal'  => $callback,
                'attributeInt'      => $callback,
                'attributeText'     => $callback,
                'attributeVarchar'  => $callback,
            ]);
        } else {
            $builder->with([
                'attributeDatetime',
                'attributeDecimal',
                'attributeInt',
                'attributeText',
                'attributeVarchar',
            ]);
        }
    }

    public function scopeAttributeHas(Builder $builder, string $attributeCode, callable $callback)
    {
        $type = Attribute::getCached()[$attributeCode]->backend_type ?? 'varchar';

        return $builder->whereHas(
            'attribute' . ucfirst($type),
            $callback,
        );
    }

    public function scopeWhereAttribute(Builder $builder, string $attributeCode, $operator = null, $value = null)
    {
        return $builder->attributeHas(
            $attributeCode,
            fn ($query) => $query->where('value', $operator, $value)->where('attribute_code', $attributeCode)
        );
    }

    public function attributeDatetime(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeDatetime::class,
            'datetime',
            'entity_id',
            'entity_id',
        );
    }

    public function attributeDecimal(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeDecimal::class,
            'decimal',
            'entity_id',
            'entity_id',
        );
    }

    public function attributeInt(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeInt::class,
            'int',
            'entity_id',
            'entity_id',
        );
    }

    public function attributeText(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeText::class,
            'text',
            'entity_id',
            'entity_id',
        );
    }

    public function attributeVarchar(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeVarchar::class,
            'varchar',
            'entity_id',
            'entity_id',
        );
    }

    public function hasManyWithAttributeTypeTable($class, $type, $foreignKey = null, $localKey = null)
    {
        $table = ($this->attributesTablePrefix ?? $this->table) . '_' . $type;

        $relation = $this->hasMany($class, $foreignKey, $localKey);
        $relation->getModel()->setTable($table);
        $relation->getQuery()->from($table);

        return $relation;
    }

    public function customAttributes(): AttributeCast
    {
        return AttributeCast::get(function () {
            if (! $this->relationLoaded('attributeDatetime')) {
                return collect();
            }

            return collect()
                ->concat($this->attributeDatetime)
                ->concat($this->attributeDecimal)
                ->concat($this->attributeInt)
                ->concat($this->attributeText)
                ->concat($this->attributeVarchar)
                ->keyBy('attribute_code');
        });
    }

    public function getCustomAttribute($key)
    {
        return $this->customAttributes[$key] ?? null;
    }
}
