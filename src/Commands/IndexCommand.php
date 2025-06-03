<?php

namespace Rapidez\Core\Commands;

use Illuminate\Console\Command;
use Rapidez\Core\Events\IndexAfterEvent;
use Rapidez\Core\Events\IndexBeforeEvent;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Traits\Searchable;

class IndexCommand extends Command
{
    protected $signature = 'rapidez:index {--t|types= : To specify types of models to index, separated by commas} {--s|store= : To specify store IDs from Magento, separated by commas}';

    protected $description = 'Index all searchable models into ElasticSearch';

    public function handle()
    {
        $baseSearchableModels = collect(config('rapidez.models'))
            ->filter(fn ($class) => in_array(Searchable::class, class_uses_recursive($class)));

        $types = $this->option('types')
            ? $baseSearchableModels->filter(fn ($model) => in_array($model::getIndexName(), explode(',', $this->option('types'))))
            : $baseSearchableModels;

        $stores = $this->option('store')
            ? Rapidez::getStores(explode(',', $this->option('store')))
            : Rapidez::getStores();

        $this->call('cache:clear');

        IndexBeforeEvent::dispatch($this);

        foreach ($stores as $store) {
            Rapidez::setStore($store);

            $this->line('Store: ' . $store['name']);

            foreach ($types as $model) {
                $searchableAs = (new $model)->searchableAs();

                $indexMapping = $model::getIndexMapping();
                if ($indexMapping) {
                    config()->set('elasticsearch.indices.mappings.' . $searchableAs, $indexMapping);
                }

                $indexSettings = $model::getIndexSettings();
                if ($indexSettings) {
                    config()->set('elasticsearch.indices.settings.' . $searchableAs, $indexSettings);
                }

                $this->call('scout:import', [
                    'searchable' => $model,
                ]);
            }
        }

        IndexAfterEvent::dispatch($this);
    }
}
