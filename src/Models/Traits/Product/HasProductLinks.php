<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\ProductLink;

trait HasProductLinks
{
    public function productLinks(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.product_link', ProductLink::class),
            'product_id', 'entity_id',
        );
    }

    public function productLinkParents(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.product_link', ProductLink::class),
            'linked_product_id', 'entity_id',
        );
    }

    public function linkedProducts(string $type): HasMany
    {
        return $this->productLinks()->where('code', $type);
    }

    public function linkedParents(string $type): HasMany
    {
        return $this->productLinkParents()->where('code', $type);
    }

    public function relationProducts(): HasMany
    {
        return $this->linkedProducts('relation');
    }

    public function superProducts(): HasMany
    {
        return $this->linkedProducts('super');
    }

    public function upsells(): HasMany
    {
        return $this->linkedProducts('up_sell');
    }

    public function crosssells(): HasMany
    {
        return $this->linkedProducts('cross_sell');
    }
}
