<?php

namespace Rapidez\Core\Commands;

use Rapidez\Core\Facades\Rapidez;
use TorMorten\Eventy\Facades\Eventy;

class IndexCategoriesCommand extends ElasticsearchIndexCommand
{
    protected $signature = 'rapidez:index:categories {store? : Store ID from Magento}';

    protected $description = 'Index the Rapidez categories to Elasticsearch';

    public function handle(): int
    {
        $this->onlyStores($this->argument('store'))
            ->withFilter(fn ($data) => Eventy::filter('index.category.data', $data))
            ->chunk(25)
            ->index(
                indexName: 'categories',
                items: $this->getCategories(...),
                id: 'entity_id'
            );

        return 0;
    }

    public function getCategories()
    {
        return config('rapidez.models.category')::query()
            ->select((new (config('rapidez.models.category')))->qualifyColumns(['entity_id', 'name', 'url_path', 'children_count']))
            ->whereNotNull('url_key')
            ->whereNot('url_key', 'default-category');
    }
}
