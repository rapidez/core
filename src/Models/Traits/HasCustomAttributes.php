<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as AttributeCast;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\AttributeDatetime;
use Rapidez\Core\Models\AttributeDecimal;
use Rapidez\Core\Models\AttributeInt;
use Rapidez\Core\Models\AttributeText;
use Rapidez\Core\Models\AttributeVarchar;

trait HasCustomAttributes
{
    protected function getCustomAttributeTypes(): array
    {
        return $this->attributeTypes ?? ['datetime', 'decimal', 'int', 'text', 'varchar'];
    }

    protected function getCustomAttributeCode(): string
    {
        return $this->attributeCode ?? 'attribute_code';
    }

    protected static function withCustomAttributes()
    {
        static::addGlobalScope('customAttributes', fn (Builder $builder) => $builder->withCustomAttributes());
    }

    public function scopeWithCustomAttributes(Builder $builder, ?callable $callback = null)
    {
        $relations = Arr::map($this->getCustomAttributeTypes(), fn($type) => 'attribute' . ucfirst($type));
        if ($callback) {
            $relations = Arr::mapWithKeys($relations, fn($relation) => [$relation => $callback]);
        }

        $builder->with($relations);
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
            fn ($query) => $query->where('value', $operator, $value)->where($this->getCustomAttributeCode(), $attributeCode)
        );
    }

    public function attributeDatetime(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeDatetime::class,
            'datetime',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function attributeDecimal(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeDecimal::class,
            'decimal',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function attributeInt(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeInt::class,
            'int',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function attributeText(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeText::class,
            'text',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function attributeVarchar(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            AttributeVarchar::class,
            'varchar',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function hasManyWithAttributeTypeTable($class, $type, $foreignKey = null, $localKey = null): HasMany
    {
        $table = ($this->attributeTablePrefix ?? $this->table) . '_' . $type;

        // Set the relation with the custom table
        $relation = $this->hasMany($class, $foreignKey, $localKey);
        $relation->getModel()->setTable($table);
        $relation->getQuery()->from($table);

        return $this->modifyRelation($relation);
    }

    protected function modifyRelation(HasMany $relation): HasMany
    {
        return $relation;
    }

    public function customAttributes(): AttributeCast
    {
        return AttributeCast::get(function () {
            $data = collect();

            foreach ($this->getCustomAttributeTypes() as $type) {
                $values = $this->{'attribute' . ucfirst($type)} ?? null;

                if ($values) {
                    $data->push(...$values);
                }
            }

            return $data->keyBy($this->getCustomAttributeCode());
        });
    }

    public function getCustomAttribute($key)
    {
        return $this->customAttributes[$key] ?? null;
    }

    public function getAttribute($key)
    {
        if (! $key) {
            return;
        }

        $value = parent::getAttribute($key);
        if ($value !== null) {
            return $value;
        }

        return $this->getCustomAttribute($key)?->value;
    }
}
