<?php

namespace Rapidez\Core\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesOrderPayment extends Model
{
    public $timestamps = false;

    protected $table = 'sales_order_payment';

    protected $primaryKey = 'entity_id';

    protected $casts = [
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
