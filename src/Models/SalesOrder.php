<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /** @return BelongsTo<Quote, SalesOrder> */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.quote'), 'quote_id');
    }

    /** @return HasMany<SalesOrderAddress, SalesOrder> */
    public function sales_order_addresses(): HasMany
    {
        return $this->hasMany(config('rapidez.models.sales_order_address'), 'parent_id');
    }

    /** @return HasMany<SalesOrderItem, SalesOrder> */
    public function sales_order_items(): HasMany
    {
        return $this->hasMany(config('rapidez.models.sales_order_item'), 'order_id');
    }

    /** @return HasMany<SalesOrderPayment, SalesOrder> */
    public function sales_order_payments(): HasMany
    {
        return $this->hasMany(config('rapidez.models.sales_order_payment'), 'parent_id');
    }

    /** @param Builder<SalesOrder> $query */
    public function scopeWhereQuoteIdOrCustomerToken(Builder $query, string $quoteIdMaskOrCustomerToken): void
    {
        $query->whereHas(
            'quote',
            fn ($query) => $query
                ->withoutGlobalScopes()
                ->whereQuoteIdOrCustomerToken($quoteIdMaskOrCustomerToken)
        );
    }
}
