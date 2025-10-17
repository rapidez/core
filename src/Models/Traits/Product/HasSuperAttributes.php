<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasSuperAttributes
{
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
        )->orderBy('catalog_product_super_attribute.position');
    }

    public function getSuperAttributesAttribute()
    {
        return $this->getRelationValue('superAttributes')->keyBy('attribute_id');
    }

    public function superAttributeValues(): Attribute
    {
        return Attribute::get(function () {
            return $this->superAttributes
                ->sortBy('position')
                ->mapWithKeys(fn ($attribute) => [
                    $attribute->attribute_code => $this->children->mapWithKeys(function ($child) use ($attribute) {
                        return [$child->entity_id => $child->{$attribute->attribute_code}];
                    })
                    ->unique('value')
                    ->sortBy('sort_order'),
                ]);
        });
    }
}
