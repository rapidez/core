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
        $this->indexStores(
            stores: Rapidez::getStores($this->argument('store')),
            indexName: 'categories',
            items: $this->getCategories(...),
            dataFilter: fn ($data) => Eventy::filter('index.category.data', $data),
        );

        return 0;
    }

    public function getCategories()
    {
        return config('rapidez.models.category')
            ::select((new (config('rapidez.models.category')))->qualifyColumns(['entity_id', 'name', 'url_path']))
            ->withCount('productIndices')
            ->whereNotNull('url_key')
            ->whereNot('url_key', 'default-category')
            ->having('product_indices_count', '>', 0)
            ->get() ?? [];
    }
}
