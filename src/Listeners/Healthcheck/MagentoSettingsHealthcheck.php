<?php

namespace Rapidez\Core\Listeners\Healthcheck;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PDOException;
use Rapidez\Core\Facades\Rapidez;

class MagentoSettingsHealthcheck extends Base
{
    public function handle()
    {
        $response = [
            'healthy'  => true,
            'messages' => [],
        ];

        try {
            DB::connection()->getPDO();
            DB::connection()->getDatabaseName();
        } catch (PDOException $e) {
            $response['healthy'] = false;
            $response['messages'][] = ['type' => 'error', 'value' => __('Database connection could not be established! :message', ['message' => PHP_EOL . $e->getMessage()])];

            return $response;
        }

        if (! Rapidez::config('catalog/frontend/flat_catalog_product', 0)) {
            $response['messages'][] = ['type' => 'error', 'value' => __(
                'The product flat tables are disabled!' . PHP_EOL .
                'Please enable them; see: https://docs.rapidez.io/3.x/installation.html#flat-tables'
            )];
        }

        $productModel = config('rapidez.models.product');
        $flatTable = (new $productModel)->getTable();
        if (! DB::getSchemaBuilder()->hasTable($flatTable)) {
            $response['healthy'] = false;
            $response['messages'][] = ['type' => 'error', 'value' => __('Flat table ":flatTable" is missing! Don\'t forget to run bin/magento indexer:reindex', ['flatTable' => $flatTable])];
        }

        if (! Rapidez::config('catalog/frontend/flat_catalog_category', 0)) {
            $response['messages'][] = ['type' => 'error', 'value' => __('The category flat tables are disabled!')];
        }

        $categoryModel = config('rapidez.models.category');
        $flatTable = (new $categoryModel)->getTable();
        if (! DB::getSchemaBuilder()->hasTable($flatTable)) {
            $response['healthy'] = false;
            $response['messages'][] = ['type' => 'error', 'value' => __('Flat table ":flatTable" is missing! Don\'t forget to run bin/magento indexer:reindex', ['flatTable' => $flatTable])];
        }

        $attributeModel = config('rapidez.models.attribute');
        $nonFlatAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return ! $attribute['flat'] && ($attribute['listing'] || $attribute['filter'] || $attribute['productpage']);
        }), 'code');

        if (! empty($nonFlatAttributes)) {
            $response['messages'][] = [
                'type'  => 'warn',
                'value' => __(
                    'Not all attributes are in the flat table: :nonFlatAttributes' . PHP_EOL .
                    'You should enable "Used in Product Listing" on them!' . PHP_EOL .
                    'More information: https://docs.rapidez.io/3.x/installation.html#flat-tables',
                    ['nonFlatAttributes' => PHP_EOL . '- ' . implode(PHP_EOL . '- ', $nonFlatAttributes)]
                ),
            ];
        }

        $nonFlatSuperAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return ! $attribute['flat'] && $attribute['super'];
        }), 'code');

        if (! empty($nonFlatSuperAttributes)) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __(
                    'Super attributes are missing from the flat table: :nonFlatAttributes',
                    ['nonFlatAttributes' => PHP_EOL . '- ' . implode(PHP_EOL . '- ', $nonFlatSuperAttributes)]
                ),
            ];
        }

        $superAttributesCount = count($attributeModel::getCachedWhere(fn ($attribute) => $attribute['super'] && $attribute['flat']));
        $joinCount = ($superAttributesCount * 2) + (count($nonFlatAttributes) * 3) + 4;

        if ($joinCount > 58) {
            $response['healthy'] = false;
            $response['messages'][] = ['type' => 'error', 'value' => __('Most likely the queries needed for Rapidez will exceed 58 joins when indexing or viewing products so you have to reduce them by adding more attributes to the flat tables')];
        }

        if (! $response['healthy']) {
            $response['messages'][] = ['type' => 'info', 'value' => __('The Magento settings have been checked, there were some errors; fix them before you continue. See: https://docs.rapidez.io/1.x/installation.html#flat-tables')];
        }

        return $response;
    }
}
