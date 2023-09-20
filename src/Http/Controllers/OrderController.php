<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;

class OrderController
{
    public function __invoke(Request $request)
    {
        $salesOrder = config('rapidez.models.sales_order')::with('sales_order_addresses', 'sales_order_items', 'sales_order_payments')
            ->whereQuoteIdOrCustomerToken($request->bearerToken())
            ->latest()
            ->firstOrFail();

        return $salesOrder;
    }
}
