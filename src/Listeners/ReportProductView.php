<?php

namespace Rapidez\Core\Listeners;

use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Facades\Rapidez;

class ReportProductView
{
    public function handle(ProductViewEvent $event)
    {
        if (! Rapidez::config('reports/options/enabled') || ! Rapidez::config('reports/options/product_view_enabled')) {
            return;
        }

        $event->product->views()->create();
    }
}
