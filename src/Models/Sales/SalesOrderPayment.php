<?php

namespace Rapidez\Core\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesOrderPayment extends Model
{
    protected $table = 'sales_order_payment';

    protected $primaryKey = 'entity_id';

    public $timestamps = false;

    protected $casts = [
        'parent_id' => 'int',
        'base_shipping_captured' => 'float',
        'shipping_captured' => 'float',
        'amount_refunded' => 'float',
        'base_amount_paid' => 'float',
        'amount_canceled' => 'float',
        'base_amount_authorized' => 'float',
        'base_amount_paid_online' => 'float',
        'base_amount_refunded_online' => 'float',
        'base_shipping_amount' => 'float',
        'shipping_amount' => 'float',
        'amount_paid' => 'float',
        'amount_authorized' => 'float',
        'base_amount_ordered' => 'float',
        'base_shipping_refunded' => 'float',
        'shipping_refunded' => 'float',
        'base_amount_refunded' => 'float',
        'amount_ordered' => 'float',
        'base_amount_canceled' => 'float',
        'quote_payment_id' => 'int',
        'additional_information' => 'collection',
    ];

    protected $hidden = [
        'cc_debug_request_body',
        'cc_secure_verify',
        'cc_approval',
        'cc_last_4',
        'cc_status_description',
        'cc_debug_response_serialized',
        'cc_ss_start_month',
        'cc_cid_status',
        'cc_owner',
        'cc_exp_year',
        'cc_exp_month',
        'cc_debug_response_body',
        'cc_number_enc',
        'cc_trans_id',
        'echeck_type',
    ];

    public function sales_order()
    {
        return $this->belongsTo(config('rapidez.models.sales.order'), 'parent_id');
    }
}
