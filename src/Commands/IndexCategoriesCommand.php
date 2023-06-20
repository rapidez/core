<?php

namespace Rapidez\Core\Commands;

use Rapidez\Core\Commands\ElasticsearchIndexCommand;
use Rapidez\Core\Facades\Rapidez;
use TorMorten\Eventy\Facades\Eventy;

class IndexCategoriesCommand extends ElasticsearchIndexCommand
{
    protected $signature = 'rapidez:index:categories {store? : Store ID from Magento}';

    protected $description = 'Index the Rapidez categories to Elasticsearch';

    public function handle(): int
    {
        $categoryModel = config('rapidez.models.category');
        $categoryModelInstance = new $categoryModel;

        $this->indexStores(
            Rapidez::getStores($this->argument('store')),
            'categories',
            fn() => config('rapidez.models.category')::select($categoryModelInstance->qualifyColumns(['entity_id', 'name', 'url_path', 'children_count']))
                ->whereNotNull('url_key')->whereNot('url_key', 'default-category')
                ->where('children_count', '>', 0)
                ->get() ?? [],
            fn($data) => Eventy::filter('index.category.data', $data),
            'entity_id'
        );

        return 0;
    }
}
