<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;

class SalesOrder extends Model
{
    protected $table = 'sales_order';

    protected $primaryKey = 'entity_id';

    protected $hidden = [
        'protect_code',
        'remote_ip',
        'x_forwarded_for',
        'paypal_ipn_customer_notified',
    ];

    public function quote()
    {
        return $this->belongsTo(config('rapidez.models.quote'), 'quote_id');
    }

    public function sales_order_addresses()
    {
        return $this->hasMany(config('rapidez.models.sales_order_address'), 'parent_id');
    }

    public function sales_order_items()
    {
        return $this->hasMany(config('rapidez.models.sales_order_item'), 'order_id');
    }

    public function sales_order_payments()
    {
        return $this->hasMany(config('rapidez.models.sales_order_payment'), 'parent_id');
    }

    public function scopeWhereQuoteIdOrCustomerToken(Builder $query, string $quoteIdMaskOrCustomerToken)
    {
        $query->whereHas(
            'quote',
            fn ($query) => $query
                ->withoutGlobalScopes()
                ->whereQuoteIdOrCustomerToken($quoteIdMaskOrCustomerToken)
        );
    }
}
