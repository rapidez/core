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

    protected $esVersion = '7.6';

    public function handle()
    {
        $this->call('cache:clear');
        $this->validateMagentoSettings();
        $this->validateElasticSearchVersion();
    }

    public function validateElasticSearchVersion()
    {
        $data = json_decode(@file_get_contents(config('rapidez.es_url')));
        if (is_object($data)
            && property_exists($data, 'version')
            && $data->version->build_flavor !== 'oss'
            && !version_compare($data->version->number, $this->esVersion, '>=')
        ) {
            $this->error('Your Elasticsearch version is too low!');
            $this->error('Your version: '.$data->version->number.' ('.$data->version->build_flavor.')');
            $this->error('You need at least: '.$this->esVersion.' basic/default (non OSS version)');
        } elseif (!is_object($data) || !property_exists($data, 'version')) {
            $this->error('Elasticsearch is not reachable at: '.config('rapidez.es_url'));
        }
    }

    public function validateMagentoSettings()
    {
        $configModel = config('rapidez.models.config');
        $attributeModel = config('rapidez.models.attribute');
        if (!$configModel::getCachedByPath('catalog/frontend/flat_catalog_category', 0) || !$configModel::getCachedByPath('catalog/frontend/flat_catalog_product', 0)) {
            $this->error('The flat tables are disabled!');
        }

        $nonFlatAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return !$attribute['flat'] && ($attribute['listing'] || $attribute['filter'] || $attribute['productpage']);
        }), 'code');

        if (!empty($nonFlatAttributes)) {
            $this->warn('Not all attributes are in the flat table: '.PHP_EOL.'- '.implode(PHP_EOL.'- ', $nonFlatAttributes));
        }

        $superAttributesCount = count($attributeModel::getCachedWhere(fn ($attribute) => $attribute['super']));
        $joinCount = ($superAttributesCount * 2) + (count($nonFlatAttributes) * 3) + 4;

        if ($joinCount > 61) {
            $this->error('Most likely the queries needed for Rapidez will exceed 61 joins when indexing or viewing products so you have to reduce them by adding more attributes to the flat tables');
        }

        $this->info('The validation finished, if there where any errors; fix them before you continue. See: https://rapidez.io/docs/0.x/installation#flat-tables');
    }
}
