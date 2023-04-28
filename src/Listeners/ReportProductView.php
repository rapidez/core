<?php

namespace Rapidez\Core\Listeners;

use Illuminate\Support\Facades\DB;
use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Facades\Rapidez;

class ReportProductView
{
    public function handle(ProductViewEvent $event)
    {
        if (!Rapidez::config('reports/options/enabled') || !Rapidez::config('reports/options/product_view_enabled')) {
            return;
        }

        DB::table('report_viewed_product_index')->insert([
            'product_id' => $event->product->id,
            'store_id'   => config('rapidez.store'),
        ]);
    }
}
