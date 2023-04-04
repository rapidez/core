<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Models\Sales\SalesOrder;

class CartOrderController
{
    public function __invoke($quoteIdMaskOrCustomerToken = '')
    {
        $salesOrder = config('rapidez.models.sales.order')::with('sales_order_addresses', 'sales_order_items', 'sales_order_payments')
            ->whereQuoteIdOrCustomerToken($quoteIdMaskOrCustomerToken)
            ->latest()
            ->firstOrFail();

        return $salesOrder;
    }
}
