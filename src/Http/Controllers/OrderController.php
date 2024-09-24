<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;

class OrderController
{
    public function __invoke(Request $request)
    {
        if (!$request->bearerToken()) {
            \Illuminate\Support\Facades\Log::debug($request->headers->__toString());
        }

        abort_unless($request->bearerToken(), 401);

        $salesOrder = config('rapidez.models.sales_order')::with('sales_order_addresses', 'sales_order_items.product', 'sales_order_payments')
            ->whereQuoteIdOrCustomerToken($request->bearerToken())
            ->latest()
            ->firstOrFail();

        return $salesOrder;
    }
}
