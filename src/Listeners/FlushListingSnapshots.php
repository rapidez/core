<?php

namespace Rapidez\Core\Listeners;

use Rapidez\Core\Commands\IndexCommand;
use Rapidez\Core\Events\IndexStoreAfterEvent;
use Rapidez\Core\Search\ListingSnapshotStore;

class FlushListingSnapshots
{
    public function handle(IndexStoreAfterEvent $event)
    {
        // The update index runs every minute, even when nothing changed.
        if (! $event->context instanceof IndexCommand) {
            return;
        }

        app(ListingSnapshotStore::class)->flush($event->store);
    }
}
