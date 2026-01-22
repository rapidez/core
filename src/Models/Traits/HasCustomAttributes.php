<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as AttributeCast;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\AttributeDatetime;
use Rapidez\Core\Models\AttributeDecimal;
use Rapidez\Core\Models\AttributeInt;
use Rapidez\Core\Models\AttributeText;
use Rapidez\Core\Models\AttributeVarchar;

trait HasCustomAttributes
{
    use HasToArrayData;

    // Hide the EAV relations for serialization.
    public function getHidden()
    {
        return array_merge(
            parent::getHidden(),
            $this->getCustomAttributeRelations()
        );
    }

    // Add the EAV attributes for serialization.
    public function customAttributesToArrayData(): array
    {
        return $this->customAttributes->toArray();
    }

    protected function getCustomAttributeTypes(): array
    {
        return property_exists(self::class, 'attributeTypes')
            ? $this->attributeTypes
            : ['datetime', 'decimal', 'int', 'text', 'varchar'];
    }

    protected function getCustomAttributeRelations(): array
    {
        return Arr::map($this->getCustomAttributeTypes(), fn ($type) => 'attribute' . ucfirst($type));
    }

    protected function getCustomAttributeCode(): string
    {
        return property_exists(self::class, 'attributeCode')
            ? $this->attributeCode
            : 'attribute_code';
    }

    protected static function withCustomAttributes()
    {
        static::addGlobalScope('customAttributes', fn (Builder $builder) => $builder->withCustomAttributes());
    }

    public function scopeWithCustomAttributes(Builder $builder, ?callable $callback = null)
    {
        $relations = $this->getCustomAttributeRelations();
        if ($callback) {
            $relations = Arr::mapWithKeys($relations, fn ($relation) => [$relation => $callback]);
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

    public function scopeWhereMethodAttribute(Builder $builder, string $method, string $attributeCode, ...$args)
    {
        if (! method_exists(\Illuminate\Database\Query\Builder::class, $method)) {
            throw new InvalidArgumentException('Method is not a valid method');
        }
        $isOrMethod = str_starts_with($method, 'or');

        if ($isOrMethod) {
            $method = lcfirst(substr($method, 2));
        }

        $q = fn ($builder) => $builder->attributeHas(
            $attributeCode,
            fn ($query) => $query
                ->$method('value', ...$args)
                ->where($this->getCustomAttributeCode(), $attributeCode)
        );

        return $isOrMethod ? $builder->orWhere($q) : $q($builder);
    }

    public function scopeWhereAttribute(Builder $builder, string $attributeCode, $operator = null, $value = null)
    {
        return $builder->whereMethodAttribute('where', $attributeCode, $operator, $value);
    }

    public function scopeWhereInAttribute(Builder $builder, string $attributeCode, $values = null, $boolean = 'and', $not = false)
    {
        return $builder->whereMethodAttribute('whereIn', $attributeCode, $values, $boolean, $not);
    }

    public function attributeDatetime(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            config('rapidez.models.attribute_datetime', AttributeDatetime::class),
            'datetime',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function attributeDecimal(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            config('rapidez.models.attribute_decimal', AttributeDecimal::class),
            'decimal',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function attributeInt(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            config('rapidez.models.attribute_int', AttributeInt::class),
            'int',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function attributeText(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            config('rapidez.models.attribute_text', AttributeText::class),
            'text',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function attributeVarchar(): HasMany
    {
        return $this->hasManyWithAttributeTypeTable(
            config('rapidez.models.attribute_varchar', AttributeVarchar::class),
            'varchar',
            $this->primaryKey,
            $this->primaryKey,
        );
    }

    public function hasManyWithAttributeTypeTable($class, $type, $foreignKey = null, $localKey = null): HasMany
    {
        $table = property_exists(self::class, 'attributeTablePrefix')
            ? $this->attributeTablePrefix
            : $this->table;

        $table .= '_' . $type;

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

            foreach ($this->getCustomAttributeRelations() as $relation) {
                if ($values = $this->$relation) {
                    $data->push(...$values);
                }
            }

            return $data->keyBy($this->getCustomAttributeCode());
        })->shouldCache();
    }

    public function getCustomAttribute($key)
    {
        return $this->customAttributes[$key] ?? null;
    }

    protected function throwMissingAttributeExceptionIfApplicable($key)
    {
        return $this->getCustomAttribute($key);
    }
}
