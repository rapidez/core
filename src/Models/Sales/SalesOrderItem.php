<?php

namespace Rapidez\Core\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $table = 'sales_order_item';

    protected $primaryKey = 'item_id';

    protected $casts = [
        'product_options' => 'collection',
    ];

    public function sales_order()
    {
        return $this->belongsTo(config('rapidez.models.sales.order'), 'order_id');
    }
}
