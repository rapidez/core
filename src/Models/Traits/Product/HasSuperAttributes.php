<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasSuperAttributes
{
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
                        return [$child->entity_id => [
                            'label'      => $child->{$attribute->attribute_code},
                            'value'      => $child->customAttributes[$attribute->attribute_code]->rawValue,
                            'sort_order' => $child->customAttributes[$attribute->attribute_code]->sort_order,
                        ]];
                    })
                    ->unique('value')
                    ->sortBy('sort_order'),
                ]);
        });
    }
}
