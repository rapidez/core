<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\Attribute as ModelsAttribute;
use Rapidez\Core\Models\AttributeDateTime;
use Rapidez\Core\Models\AttributeDecimal;
use Rapidez\Core\Models\AttributeInt;
use Rapidez\Core\Models\AttributeText;
use Rapidez\Core\Models\AttributeVarchar;
use Rapidez\Core\Models\EavAttribute;

trait HasCustomAttributes
{
    public function scopeWithCustomAttributes(Builder $builder)
    {
        $builder->with([
            'attributeDateTime',
            'attributeDecimal',
            'attributeInt',
            'attributeText',
            'attributeVarchar',
        ]);
    }

    public function scopeWhereValue(Builder $builder, string $attributeCode, $operator = null, $value = null)
    {
        $type = EavAttribute::getCached()[$attributeCode]->backend_type ?? 'varchar';
        $relation = match($type) {
            'datetime' => 'attributeDateTime',
            default => 'attribute' . ucfirst($type),
        };

        return $builder->whereHas($relation, fn ($query) =>
            $query->where('value', $operator, $value)->where('attribute_code', $attributeCode)
        );
    }

    public function attributeDateTime(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeDateTime::class,
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

    public function customAttributes(): Attribute
    {
        return Attribute::get(function () {
            if (@! $this->attributeDateTime) {
                return collect();
            }

            return collect()
                ->concat($this->attributeDateTime)
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
