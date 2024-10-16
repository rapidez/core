<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Rapidez\Core\Models\SalesOrder;

class OrderController
{
    public function __invoke(Request $request): SalesOrder
    {
        abort_unless(boolval($request->bearerToken()), 401);

        /** @var SalesOrder $salesOrder */
        $salesOrder = config('rapidez.models.sales_order')::with('sales_order_addresses', 'sales_order_items.product', 'sales_order_payments')
            ->whereQuoteIdOrCustomerToken($request->bearerToken())
            ->latest()
            ->firstOrFail();

        return $salesOrder;
    }
}
