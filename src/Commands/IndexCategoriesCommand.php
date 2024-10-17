<?php

namespace Rapidez\Core\Commands;

use Illuminate\Database\Eloquent\Collection;
use Rapidez\Core\Facades\Rapidez;
use TorMorten\Eventy\Facades\Eventy;

class IndexCategoriesCommand extends ElasticsearchIndexCommand
{
    protected $signature = 'rapidez:index:categories {store? : Store ID from Magento}';

    protected $description = 'Index the Rapidez categories to Elasticsearch';

    public function handle(): int
    {
        $this->synonymsFor = ['name'];

        $this->indexStores(
            stores: Rapidez::getStores($this->argument('store')),
            indexName: 'categories',
            items: $this->getCategories(...),
            dataFilter: fn ($data) => Eventy::filter('index.category.data', $data),
        );

        return 0;
    }

    /** @return Collection<int, \Rapidez\Core\Models\Category> */
    public function getCategories(): Collection
    {
        $categoryModel = config('rapidez.models.category');
        /** @var \Rapidez\Core\Models\Category $categoryObject */
        $categoryObject = new $categoryModel;

        return $categoryModel::withEventyGlobalScopes('index.categories.scopes')
            ->select($categoryObject->qualifyColumns(['entity_id', 'name', 'url_path']))
            ->whereNotNull('url_key')
            ->whereNot('url_key', 'default-category')
            ->has('products')
            ->get();
    }
}
