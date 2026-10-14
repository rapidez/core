<?php

namespace Rapidez\Core\Listeners;

use Illuminate\Routing\Events\ResponsePrepared;
use Rapidez\Core\Search\ListingSnapshotStore;
use TorMorten\Eventy\Facades\Eventy;

class PreventCachingIncompleteListingSnapshots
{
    // After the view is rendered, but within the middleware of a full page cache.
    public function handle(ResponsePrepared $event)
    {
        if (! config('rapidez.ssr.enabled') || app(ListingSnapshotStore::class)->cacheable($event->request)) {
            return;
        }

        Eventy::filter('uncacheable.response', $event->response);
    }
}
