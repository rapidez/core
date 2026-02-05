<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Rapidez\Core\Models\Relations\HasManyCallback;
use Rapidez\Core\Models\Traits\HasToArrayData;
use Rapidez\Core\Models\Traits\UsesCallbackRelations;

trait HasSuperAttributes
{
    use HasToArrayData;
    use UsesCallbackRelations;

    /**
     * @deprecated please use superAttributes
     */
    public function super_attributes(): HasManyCallback
    {
        return $this->superAttributes();
    }

    public function superAttributes(): HasManyCallback
    {
        return $this->hasManyCallback(
            fn ($result) => $result->keyBy('attribute_id'),
            config('rapidez.models.super_attribute'),
            'product_id',
        )
            ->orderBy('eav_attribute.attribute_code')
            ->orderBy('catalog_product_super_attribute.position');
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
