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
     * @deprecated please use $product->reviewSummary->reviews_count
     */
    protected function reviewsCount(): Attribute
    {
        return Attribute::get(fn () => $this->reviewSummary->reviews_count);
    }

    /**
     * @deprecated please use $product->reviewSummary->rating_summary
     */
    protected function reviewsScore(): Attribute
    {
        return Attribute::get(fn () => $this->reviewSummary->rating_summary);
    }
}
