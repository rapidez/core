<?php

namespace Rapidez\Core\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesOrderAddress extends Model
{
    protected $table = 'sales_order_address';

    protected $primaryKey = 'entity_id';

    public $timestamps = false;

    protected $casts = [
        'parent_id' => 'int',
        'customer_address_id' => 'int',
        'quote_address_id' => 'int',
        'region_id' => 'int',
        'customer_id' => 'int',
        'vat_is_valid' => 'int',
        'vat_request_success' => 'int',
    ];

    public function sales_order()
    {
        return $this->belongsTo(config('rapidez.models.sales.order'), 'parent_id');
    }
}
