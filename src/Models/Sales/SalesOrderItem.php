<?php

namespace Rapidez\Core\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $table = 'sales_order_item';

    protected $primaryKey = 'item_id';

    protected $casts = [
        'order_id' => 'int',
        'parent_item_id' => 'int',
        'quote_item_id' => 'int',
        'store_id' => 'int',
        'product_id' => 'int',
        'product_options' => 'collection',
        'weight' => 'float',
        'is_virtual' => 'int',
        'is_qty_decimal' => 'int',
        'no_discount' => 'int',
        'qty_backordered' => 'float',
        'qty_canceled' => 'float',
        'qty_invoiced' => 'float',
        'qty_ordered' => 'float',
        'qty_refunded' => 'float',
        'qty_shipped' => 'float',
        'base_cost' => 'float',
        'price' => 'float',
        'base_price' => 'float',
        'original_price' => 'float',
        'base_original_price' => 'float',
        'tax_percent' => 'float',
        'tax_amount' => 'float',
        'base_tax_amount' => 'float',
        'tax_invoiced' => 'float',
        'base_tax_invoiced' => 'float',
        'discount_percent' => 'float',
        'discount_amount' => 'float',
        'base_discount_amount' => 'float',
        'discount_invoiced' => 'float',
        'base_discount_invoiced' => 'float',
        'amount_refunded' => 'float',
        'base_amount_refunded' => 'float',
        'row_total' => 'float',
        'base_row_total' => 'float',
        'row_invoiced' => 'float',
        'base_row_invoiced' => 'float',
        'row_weight' => 'float',
        'base_tax_before_discount' => 'float',
        'tax_before_discount' => 'float',
        'locked_do_invoice' => 'int',
        'locked_do_ship' => 'int',
        'price_incl_tax' => 'float',
        'base_price_incl_tax' => 'float',
        'row_total_incl_tax' => 'float',
        'base_row_total_incl_tax' => 'float',
        'discount_tax_compensation_amount' => 'float',
        'base_discount_tax_compensation_amount' => 'float',
        'discount_tax_compensation_invoiced' => 'float',
        'base_discount_tax_compensation_invoiced' => 'float',
        'discount_tax_compensation_refunded' => 'float',
        'base_discount_tax_compensation_refunded' => 'float',
        'tax_canceled' => 'float',
        'discount_tax_compensation_canceled' => 'float',
        'tax_refunded' => 'float',
        'base_tax_refunded' => 'float',
        'discount_refunded' => 'float',
        'base_discount_refunded' => 'float',
        'gift_message_id' => 'int',
        'gift_message_available' => 'int',
        'free_shipping' => 'int',
        'weee_tax_applied_amount' => 'float',
        'weee_tax_applied_row_amount' => 'float',
        'weee_tax_disposition' => 'float',
        'weee_tax_row_disposition' => 'float',
        'base_weee_tax_applied_amount' => 'float',
        'base_weee_tax_applied_row_amnt' => 'float',
        'base_weee_tax_disposition' => 'float',
        'base_weee_tax_row_disposition' => 'float',
    ];

    public function sales_order()
    {
        return $this->belongsTo(config('rapidez.models.sales.order'), 'order_id');
    }
}
