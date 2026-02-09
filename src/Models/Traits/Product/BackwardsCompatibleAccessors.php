<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait BackwardsCompatibleAccessors
{
    /**
     * @deprecated please use $product->stock->min_sale_qty
     */
    protected function minSaleQty(): Attribute
    {
        return Attribute::get(fn () => $this->stock->min_sale_qty);
    }

    /**
     * @deprecated please use $product->stock->max_sale_qty
     */
    protected function maxSaleQty(): Attribute
    {
        return Attribute::get(fn () => $this->stock->max_sale_qty);
    }

    /**
     * @deprecated please use $product->stock->qty_increments
     */
    protected function qtyIncrements(): Attribute
    {
        return Attribute::get(fn () => $this->stock->qty_increments);
    }

    /**
     * @deprecated please use $product->stock->is_in_stock
     */
    protected function inStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock->is_in_stock);
    }

    /**
     * @deprecated please use $product->upsells()->pluck('linked_product_id')
     */
    public function upsellIds(): Attribute
    {
        return Attribute::get(fn () =>
            $this->upsells()->pluck('linked_product_id')
        );
    }

    /**
     * @deprecated please use $product->relationProducts()->pluck('linked_product_id')
     */
    public function relationIds(): Attribute
    {
        return Attribute::get(fn () =>
            $this->relationProducts()->pluck('linked_product_id')
        );
    }
}
