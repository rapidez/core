<?php

namespace Rapidez\Core\Commands;

use Illuminate\Console\Command;
use Rapidez\Core\Events\IndexAfterEvent;
use Rapidez\Core\Events\IndexBeforeEvent;
use Rapidez\Core\Facades\Rapidez;

class IndexCommand extends Command
{
    protected $signature = 'rapidez:index {--t|types= : To specify types of models to index, separated by commas} {--s|store= : To specify store IDs from Magento, separated by commas}';

    protected $description = 'Index all searchable models in Elasticsearch';

    public function handle()
    {
        $types = $this->option('types')
            ? explode(',', $this->option('types'))
            : config('rapidez.indexer.models');
        // TODO: Where do we put the above configuration?
        // Can (and should?) we dynamically retrieve which models have the Searchable trait?

        $stores = $this->option('store')
            ? Rapidez::getStores(explode(',', $this->option('store')))
            : Rapidez::getStores();

        $this->call('cache:clear');

        IndexBeforeEvent::dispatch($this);

        foreach ($stores as $store) {
            Rapidez::setStore($store);

            $this->line('Store: ' . $store['name']);

            foreach($types as $type) {
                $this->call('scout:import', [
                    'searchable' => config('rapidez.models.' . $type),
                ]);
            }
        }

        IndexAfterEvent::dispatch($this);
    }
}
