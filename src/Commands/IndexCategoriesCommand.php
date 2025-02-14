<?php

namespace Rapidez\Core\Commands;

use Illuminate\Console\Command;

class IndexCategoriesCommand extends Command
{
    protected $signature = 'rapidez:index:categories {store? : Store ID from Magento}';

    protected $description = 'Index the Rapidez categories to Elasticsearch';

    public function handle(): int
    {
        // TODO: (re)implement with Laravel Scout
        // Move it to the caregory model?
        return 0;

        // $this->synonymsFor = ['name'];

        // $this->indexStores(
        //     stores: Rapidez::getStores($this->argument('store')),
        //     indexName: 'categories',
        //     items: $this->getCategories(...),
        //     dataFilter: fn ($data) => Eventy::filter('index.category.data', $data),
        // );

        // return 0;
    }

    // public function getCategories()
    // {
    //     return config('rapidez.models.category')::withEventyGlobalScopes('index.categories.scopes')
    //         ->select((new (config('rapidez.models.category')))->qualifyColumns(['entity_id', 'name', 'url_path']))
    //         ->whereNotNull('url_key')
    //         ->whereNot('url_key', 'default-category')
    //         ->has('products')
    //         ->get() ?? [];
    // }
}
