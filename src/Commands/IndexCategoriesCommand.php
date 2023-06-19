<?php

namespace Rapidez\Core\Commands;

use Rapidez\Core\Commands\ElasticsearchIndexCommand;
use Rapidez\Core\Facades\Rapidez;

class IndexCategoriesCommand extends ElasticsearchIndexCommand
{
    protected $signature = 'rapidez:index:categories {store? : Store ID from Magento}';

    protected $description = 'Index the Rapidez categories to Elasticsearch';

    public function handle(): int
    {
        $categoryModel = config('rapidez.models.category');
        $categoryModelInstance = new $categoryModel;
        $categories = config('rapidez.models.category')::select($categoryModelInstance->qualifyColumns(['entity_id', 'name', 'url_path', 'children_count']))
            ->whereNotNull('url_key')->whereNot('url_key', 'default-category')
            ->where('children_count', '>', 0)
            ->get() ?? [];

        $this->indexStores(Rapidez::getStores($this->argument('store')), 'categories', $categories, ['name', 'url_path', 'children_count'], 'entity_id');

        return 0;
    }
}
