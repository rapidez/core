<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\Traits\HasToArrayData;

trait HasSuperAttributes
{
    use HasToArrayData;

    /**
     * @deprecated please use superAttributes
     */
    public function super_attributes(): HasMany
    {
        return $this->superAttributes();
    }

    public function superAttributes(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.super_attribute'),
            'product_id',
        )
            ->orderBy('eav_attribute.attribute_code')
            ->orderBy('catalog_product_super_attribute.position')
            ->afterQuery(fn ($result) => $result->keyBy('attribute_id'));
    }

    public function superAttributeValues(): Attribute
    {
        return Attribute::get(fn () => $this->superAttributes
            ->sortBy('position')
            ->mapWithKeys(fn ($attribute) => [
                $attribute->attribute_code => $this->children
                    ->mapWithKeys(fn ($child) => [
                        $child->entity_id => $child->getCustomAttribute($attribute->attribute_code),
                    ])
                    ->sortBy('sort_order')
                    ->groupBy('rawValue')
                    ->map(fn ($children, $value) => (object) [
                        'children' => $children->pluck('entity_id'),
                        'value'    => $value,
                        'label'    => $children->first()->label,
                    ]),
            ])
        )->shouldCache();
    }

    public function superAttributesToArrayData(): array
    {
        return $this->superAttributeValues
            ->mapWithKeys(fn ($values, $attribute) => [
                "super_{$attribute}"        => $values->pluck('value'),
                "super_{$attribute}_values" => $values,
            ])
            ->toArray();
    }

    public function superAttributeCodes(): Attribute
    {
        return Attribute::get(fn () => $this->superAttributes
            ->pluck('attribute_code')->map(fn ($attribute) => [
                $attribute,
                "super_{$attribute}",
                "super_{$attribute}_values",
            ])
            ->flatten()
        );
    }
}
