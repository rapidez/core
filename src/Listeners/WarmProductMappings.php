<?php

namespace Rapidez\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Events\IndexStoreAfterEvent;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Facades\Rapidez;

class WarmProductMappings
{
    public function handle(IndexStoreAfterEvent $event)
    {
        Rapidez::setStore($event->store);

        $index = (new Product)->searchableAs();
        Cache::forget('elastic_mappings_' . $index);
        Product::getIndexedMapping();
    }
}
