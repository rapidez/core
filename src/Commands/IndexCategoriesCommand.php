<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Rapidez\Core\Commands\InteractsWithElasticsearchCommand;
use Rapidez\Core\Models\Store;

class IndexCategoriesCommand extends InteractsWithElasticsearchCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rapidez:index:categories {store? : Store ID from Magento}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index the rapidez categories to elasticsearch';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $storeModel = config('rapidez.models.store');
        $categoryModel = config('rapidez.models.category');
        $stores = $this->argument('store')
        ? $storeModel::where('store_id', $this->argument('store'))->get()
        : $storeModel::all();

        foreach ($stores as $store) {
            $this->line('Indexing categories for store: '.$store->name);
            $this->setStore($store);

            $categoryModelInstance = new $categoryModel();
            $data = config('rapidez.models.category')::select($categoryModelInstance->qualifyColumns(['entity_id', 'name', 'url_path', 'children_count']))
                ->whereNotNull('url_key')->whereNot('url_key', 'default-category')
                ->where('children_count', '>', 0)
                ->get() ?? [];

            $alias = config('rapidez.es_prefix').'_categories_'.$store->store_id;
            $index = $alias.'_'.Carbon::now()->format('YmdHis');
            $this->createIndex($index);

            foreach ($data as $item) {
                $this->elasticsearch->index([
                    'index' => $index,
                    'id'    => $item->entity_id,
                    'body'  => [
                        'title'    => $item->name,
                        'url'      => $item->url_path,
                        'content'  => $item->name,
                        'children' => $item->children_count ?? 0,
                    ],
                ]);
            }

            $this->switchAlias($alias, $index);
        }

        return 0;
    }

    private function setStore(Store $store): void
    {
        config()->set('rapidez.store', $store->store_id);
        $code = config('rapidez.models.store')::getCachedWhere(function ($store) {
            return $store['store_id'] == config('rapidez.store');
        })['code'];
        config()->set('rapidez.store_code', $code);
    }
}
