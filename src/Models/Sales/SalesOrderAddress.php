<?php

namespace Rapidez\Core\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesOrderAddress extends Model
{
    protected $table = 'sales_order_address';

    protected $primaryKey = 'entity_id';

    public $timestamps = false;

    public function sales_order()
    {
        return $this->belongsTo(config('rapidez.models.sales.order'), 'parent_id');
    }
}
