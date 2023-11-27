<?php

namespace Rapidez\Core\Commands;

use TorMorten\Eventy\Facades\Eventy;

class IndexCategoriesCommand extends ElasticsearchIndexCommand
{
    protected $signature = 'rapidez:index:categories {store? : Store ID from Magento}';

    protected $description = 'Index the Rapidez categories to Elasticsearch';

    public function handle(): int
    {
        $params = new ElasticsearchIndexParameters(
            dataFilter: fn ($data) => Eventy::filter('index.category.data', $data),
            indexName: 'categories',
            stores: $this->argument('store'),
            synonymsFor: ['name'],
        );

        $this->index($this->getCategories(...), $params);

        return 0;
    }

    public function getCategories()
    {
        return config('rapidez.models.category')::withEventyGlobalScopes('index.categories.scopes')
            ->select((new (config('rapidez.models.category')))->qualifyColumns(['entity_id', 'name', 'url_path']))
            ->whereNotNull('url_key')
            ->whereNot('url_key', 'default-category')
            ->has('products');
    }
}
