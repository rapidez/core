<?php

namespace Rapidez\Core\Commands;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Rapidez\Core\Events\IndexAfterEvent;
use Rapidez\Core\Events\IndexBeforeEvent;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Category;
use Rapidez\Core\Models\CategoryProduct;
use TorMorten\Eventy\Facades\Eventy;

class IndexProductsCommand extends ElasticsearchIndexCommand
{
    protected $signature = 'rapidez:index {store? : Store ID from Magento}';

    protected $description = 'Index the products in Elasticsearch';

    protected int $chunkSize = 500;

    public function handle(): void
    {
        $this->call('cache:clear');

        IndexBeforeEvent::dispatch($this);

        $productModel = config('rapidez.models.product');
        $stores = Rapidez::getStores($this->argument('store'));
        foreach ($stores as $store) {
            $this->line('Store: ' . $store['name']);

            // @phpstan-ignore-next-line
            $this->prepareIndexerWithStore($store, 'products', Eventy::filter('index.product.mapping', [
                'properties' => [
                    'price' => [
                        'type' => 'double',
                    ],
                    'children' => [
                        'type' => 'flattened',
                    ],
                    'grouped' => [
                        'type' => 'flattened',
                    ],
                    'positions' => [
                        'type' => 'flattened',
                    ],
                ],
            ]), Eventy::filter('index.product.settings', []), ['name']); // @phpstan-ignore-line

            try {
                $maxPositions = CategoryProduct::query()
                    ->selectRaw('GREATEST(MAX(position), 0) as position')
                    ->addSelect('category_id')
                    ->groupBy('category_id')
                    ->pluck('position', 'category_id');

                $productQuery = $productModel::selectOnlyIndexable()
                    ->with(['categoryProducts', 'reviewSummary'])
                    ->withEventyGlobalScopes('index.product.scopes')
                    ->withExists('options AS has_options');

                $bar = $this->output->createProgressBar($productQuery->getQuery()->getCountForPagination());
                $bar->start();

                $categories = Category::withEventyGlobalScopes('index.category.scopes')
                    ->where('catalog_category_flat_store_' . config('rapidez.store') . '.entity_id', '<>', Rapidez::config('catalog/category/root_id', 2))
                    ->pluck('name', 'entity_id');

                $showOutOfStock = (bool) Rapidez::config('cataloginventory/options/show_out_of_stock', 0);
                $indexVisibility = config('rapidez.indexer.visibility');

                $productQuery->chunk($this->chunkSize, function ($products) use ($store, $bar, $categories, $showOutOfStock, $indexVisibility, $maxPositions) {
                    $this->indexer->index($products, function ($product) use ($store, $categories, $showOutOfStock, $indexVisibility, $maxPositions) {
                        if (! in_array($product->visibility, $indexVisibility)) {
                            return;
                        }

                        if (! $showOutOfStock && ! $product->in_stock) {
                            return;
                        }

                        $data = array_merge(['store' => $store['store_id']], $product->toArray());

                        foreach ($product->super_attributes ?: [] as $superAttribute) {
                            $data['super_' . $superAttribute->code] = $superAttribute->text_swatch || $superAttribute->visual_swatch
                                ? array_keys((array) $product->{'super_' . $superAttribute->code})
                                : Arr::pluck($product->{'super_' . $superAttribute->code} ?: [], 'label');
                        }

                        $data = $this->withCategories($data, $categories);

                        $data['positions'] = $product->categoryProducts
                            ->pluck('position', 'category_id')
                            // Turn all positions positive
                            ->mapWithKeys(fn ($position, $category_id) => [$category_id => $maxPositions[$category_id] - $position]);

                        // @phpstan-ignore-next-line
                        return Eventy::filter('index.product.data', $data, $product);
                    });

                    $bar->advance($products->count());
                });

                $this->indexer->finish();
            } catch (Exception $e) {
                $this->indexer->abort();

                throw $e;
            }

            $bar->finish();
            $this->line('');
        }

        IndexAfterEvent::dispatch($this);
        $this->info('Done!');
    }

    /**
     * @param  array<string, array<int, string>>  $data
     * @param  Collection<int, Category>  $categories
     * @return array<string, array<int, string>>
     */
    public function withCategories(array $data, Collection $categories): array
    {
        foreach ($data['category_paths'] as $categoryPath) {
            $category = [];
            foreach (explode('/', $categoryPath) as $categoryId) {
                if (isset($categories[$categoryId])) {
                    $category[] = $categoryId . '::' . $categories[$categoryId];
                }
            }
            if (! empty($category)) {
                $data['categories'][] = implode(' /// ', $category);
            }
        }

        return $data;
    }
}
