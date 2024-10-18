<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    protected $table = 'sales_order_item';

    protected $primaryKey = 'item_id';

    protected $casts = [
        'product_options' => 'collection',
    ];

    /** @return BelongsTo<SalesOrder, SalesOrderItem> */
    public function sales_order(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.sales_order'), 'order_id');
    }

    /** @return BelongsTo<Product, SalesOrderItem> */
    public function product(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.product'), 'sku', 'sku')->selectAttributes(config('rapidez.frontend.cart_attributes'));
    }
}
