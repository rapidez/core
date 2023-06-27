<?php

namespace Rapidez\Core\Http\Controllers;

class OrderController
{
    public function __invoke($quoteIdMaskOrCustomerToken = '')
    {
        $salesOrder = config('rapidez.models.sales_order')::with('sales_order_addresses', 'sales_order_items', 'sales_order_payments')
            ->whereQuoteIdOrCustomerToken($quoteIdMaskOrCustomerToken)
            ->latest()
            ->firstOrFail();

        return $salesOrder;
    }
}
