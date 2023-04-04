<?php

namespace Rapidez\Core\Models\Sales;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Models\Customer\CustomerEntity;
use Rapidez\Core\Models\Quote\Quote;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class SalesOrder extends Model
{
    protected $table = 'sales_order';

    protected $primaryKey = 'entity_id';

    protected $casts = [
        'is_virtual' => 'int',
        'store_id' => 'int',
        'customer_id' => 'int',
        'base_discount_amount' => 'float',
        'base_discount_canceled' => 'float',
        'base_discount_invoiced' => 'float',
        'base_discount_refunded' => 'float',
        'base_grand_total' => 'float',
        'base_shipping_amount' => 'float',
        'base_shipping_canceled' => 'float',
        'base_shipping_invoiced' => 'float',
        'base_shipping_refunded' => 'float',
        'base_shipping_tax_amount' => 'float',
        'base_shipping_tax_refunded' => 'float',
        'base_subtotal' => 'float',
        'base_subtotal_canceled' => 'float',
        'base_subtotal_invoiced' => 'float',
        'base_subtotal_refunded' => 'float',
        'base_tax_amount' => 'float',
        'base_tax_canceled' => 'float',
        'base_tax_invoiced' => 'float',
        'base_tax_refunded' => 'float',
        'base_to_global_rate' => 'float',
        'base_to_order_rate' => 'float',
        'base_total_canceled' => 'float',
        'base_total_invoiced' => 'float',
        'base_total_invoiced_cost' => 'float',
        'base_total_offline_refunded' => 'float',
        'base_total_online_refunded' => 'float',
        'base_total_paid' => 'float',
        'base_total_qty_ordered' => 'float',
        'base_total_refunded' => 'float',
        'discount_amount' => 'float',
        'discount_canceled' => 'float',
        'discount_invoiced' => 'float',
        'discount_refunded' => 'float',
        'grand_total' => 'float',
        'shipping_amount' => 'float',
        'shipping_canceled' => 'float',
        'shipping_invoiced' => 'float',
        'shipping_refunded' => 'float',
        'shipping_tax_amount' => 'float',
        'shipping_tax_refunded' => 'float',
        'store_to_base_rate' => 'float',
        'store_to_order_rate' => 'float',
        'subtotal' => 'float',
        'subtotal_canceled' => 'float',
        'subtotal_invoiced' => 'float',
        'subtotal_refunded' => 'float',
        'tax_amount' => 'float',
        'tax_canceled' => 'float',
        'tax_invoiced' => 'float',
        'tax_refunded' => 'float',
        'total_canceled' => 'float',
        'total_invoiced' => 'float',
        'total_offline_refunded' => 'float',
        'total_online_refunded' => 'float',
        'total_paid' => 'float',
        'total_qty_ordered' => 'float',
        'total_refunded' => 'float',
        'can_ship_partially' => 'int',
        'can_ship_partially_item' => 'int',
        'customer_is_guest' => 'int',
        'customer_note_notify' => 'int',
        'billing_address_id' => 'int',
        'customer_group_id' => 'int',
        'edit_increment' => 'int',
        'email_sent' => 'int',
        'send_email' => 'int',
        'forced_shipment_with_invoice' => 'int',
        'payment_auth_expiration' => 'int',
        'quote_address_id' => 'int',
        'quote_id' => 'int',
        'shipping_address_id' => 'int',
        'adjustment_negative' => 'float',
        'adjustment_positive' => 'float',
        'base_adjustment_negative' => 'float',
        'base_adjustment_positive' => 'float',
        'base_shipping_discount_amount' => 'float',
        'base_subtotal_incl_tax' => 'float',
        'base_total_due' => 'float',
        'payment_authorization_amount' => 'float',
        'shipping_discount_amount' => 'float',
        'subtotal_incl_tax' => 'float',
        'total_due' => 'float',
        'weight' => 'float',
        'total_item_count' => 'int',
        'customer_gender' => 'int',
        'discount_tax_compensation_amount' => 'float',
        'base_discount_tax_compensation_amount' => 'float',
        'shipping_discount_tax_compensation_amount' => 'float',
        'base_shipping_discount_tax_compensation_amnt' => 'float',
        'discount_tax_compensation_invoiced' => 'float',
        'base_discount_tax_compensation_invoiced' => 'float',
        'discount_tax_compensation_refunded' => 'float',
        'base_discount_tax_compensation_refunded' => 'float',
        'shipping_incl_tax' => 'float',
        'base_shipping_incl_tax' => 'float',
        'gift_message_id' => 'int',
    ];

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

    public function customer_entity()
    {
        return $this->belongsTo(config('rapidez.models.customer.entity'), 'customer_id');
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

    public function scopeWhereQuoteIdMask(Builder $query, $quoteIdMask)
    {
        $query->whereHas(
            'quote',
            fn ($query) => $query
            ->withoutGlobalScope(IsActiveScope::class)
            ->whereHas(
                'quote_id_masks',
                fn ($query) => $query
                ->where('masked_id', $quoteIdMask)
            )
        );
    }

    public function scopeWhereCustomerToken(Builder $query, $customerToken)
    {
        $query->whereHas(
            'customer_entity',
            fn ($query) => $query
            ->whereHas(
                'oauth_tokens',
                fn ($query) => $query
                ->where('token', $customerToken)
            )
        );
    }

    public function scopeWhereQuoteIdOrCustomerToken(Builder $query, $quoteIdMaskOrCustomerToken)
    {
        $query->where(
            fn ($query) => $query
                ->whereQuoteIdMask($quoteIdMaskOrCustomerToken)
                ->orWhere(fn ($query) => $query->whereCustomerToken($quoteIdMaskOrCustomerToken))
        );
    }
}
