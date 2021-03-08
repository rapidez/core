<?php

namespace Rapidez\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\Config;

class ValidateCommand extends Command
{
    protected $signature = 'rapidez:validate';

    protected $description = 'Validates all settings';

    public function handle()
    {
        $this->call('cache:clear');

        if (!Config::getCachedByPath('catalog/frontend/flat_catalog_category', 0)) {
            $this->error('catalog/frontend/flat_catalog_category is disabled, you have to enable it in Magento and reindex: "bin/magento indexer:reindex"');
        }

        if (!Config::getCachedByPath('catalog/frontend/flat_catalog_product', 0)) {
            $this->error('catalog/frontend/flat_catalog_product is disabled, you have to enable it in Magento and reindex: "bin/magento indexer:reindex"');
        }

        $nonFlatAttributes = Arr::pluck(Attribute::getCachedWhere(function ($attribute) {
            return !$attribute['flat'] && ($attribute['listing'] || $attribute['filter'] || $attribute['productpage']);
        }), 'code');

        if (!empty($nonFlatAttributes)) {
            $this->warn('Not all attributes are in the flat table: '.PHP_EOL.'- '.implode(PHP_EOL.'- ', $nonFlatAttributes));
            $this->warn('It is recommended to have as much attributes in the flat table as possible. The recommended way to do this is by enabling "used_in_product_listing" (but be careful with sensitive attributes as these will end up in Elasticsearch) or "is_visible_on_front" and run the indexes afterwards: "bin/magento indexer:reindex". If you run into issues like "Row size too large" MySQL errors then you disable that option for the attributes which contains the most data until the indexes run fine.');
        }

        $superAttributesCount = count(Attribute::getCachedWhere(fn ($attribute) => $attribute['super']));
        $joinCount = ($superAttributesCount * 2) + (count($nonFlatAttributes) * 3) + 4;

        if ($joinCount > 61) {
            $this->error('Most likely the queries needed for Rapidez will exceed 61 joins when indexing or viewing products so you have to reduce them by adding more attributes to the flat tables as described!');
        }

        $this->info('The validation finished, if there where any errors; fix them before you continue.');
    }
}
