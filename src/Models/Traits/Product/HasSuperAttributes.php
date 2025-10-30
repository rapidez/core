<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Rapidez\Core\Models\SuperAttribute;

trait HasSuperAttributes
{
    /**
     * @deprecated please use superAttributes
     */
    public function super_attributes(): HasMany
    {
        return $this->superAttributes();
    }

    public static function allSuperAttributes(): Collection
    {
        return SuperAttribute::all()->pluck('attribute_code')->unique();
    }

    public function superAttributes(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.super_attribute'),
            'product_id',
        )->orderBy('catalog_product_super_attribute.position');
    }

    public function getSuperAttributesAttribute()
    {
        return $this
            ->getRelationValue('superAttributes')
            ->sortBy('attribute_code')
            ->sortBy('position')
            ->keyBy('attribute_id');
    }

    public function superAttributeValues(): Attribute
    {
        return Attribute::get(fn () =>
            $this->superAttributes
                ->sortBy('position')
                ->mapWithKeys(fn ($attribute) => [
                    $attribute->attribute_code => $this->children
                        ->mapWithKeys(fn ($child) => [
                            $child->entity_id => $child->{$attribute->attribute_code}
                        ])
                        ->sortBy('sort_order')
                        ->groupBy('value')
                        ->map(fn($children, $value) => [
                            'children' => $children,
                            'value' => $value,
                            'label' => $children->first()->label,
                        ]),
                ])
        );
    }
}
