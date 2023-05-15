<?php

namespace Rapidez\Core\Models\Sales;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class SalesOrder extends Model
{
    protected $table = 'sales_order';

    protected $primaryKey = 'entity_id';

    protected $dates = [
        'customer_dob',
    ];

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
        return $this->hasMany(config('rapidez.models.sales.order_address'), 'parent_id');
    }

    public function sales_order_items()
    {
        return $this->hasMany(config('rapidez.models.sales.order_item'), 'order_id');
    }

    public function sales_order_payments()
    {
        return $this->hasMany(config('rapidez.models.sales.order_payment'), 'parent_id');
    }

    public function scopeWhereQuoteIdOrCustomerToken(Builder $query, $quoteIdMaskOrCustomerToken)
    {
        $query->whereHas(
            'quote',
            fn ($query) => $query
                ->withoutGlobalScope(IsActiveScope::class)
                ->where('quote_id_mask.masked_id', $quoteIdMaskOrCustomerToken)
                ->orWhere('oauth_token.token', $quoteIdMaskOrCustomerToken)
        );
    }
}
