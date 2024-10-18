<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderAddress extends Model
{
    protected $table = 'sales_order_address';

    protected $primaryKey = 'entity_id';

    public $timestamps = false;

    /** @return BelongsTo<SalesOrder, SalesOrderAddress> */
    public function sales_order(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.sales_order'), 'parent_id');
    }
}
