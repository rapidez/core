<?php

namespace Rapidez\Core\Commands;

use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Store;

class ElasticsearchIndexParameters
{
    public function __construct(
        public string $indexName,
        public ?array $stores = null,
        public mixed $dataFilter = null,
        public mixed $id = 'entity_id',

        public ?int $chunkSize = null,
        public bool $progressBar = true,

        public array $mapping = [],
        public array $settings = [],
        public array $synonymsFor = [],
        public array $extraSynonyms = [],
    )
    {
        if(!$stores) {
            $this->setStores(null);
        }
    }

    public function addMapping(string $path, mixed $mapping, bool $overwrite = false): void
    {
        data_set($this->mapping, $path, $mapping, $overwrite);
    }

    public function addMappings(array $mapping): void
    {
        $this->mapping = array_merge_recursive($this->mapping, $mapping);
    }

    public function addSetting(string $path, mixed $setting, bool $overwrite = false): void
    {
        data_set($this->settings, $path, $setting, $overwrite);
    }

    public function addSettings(array $settings): void
    {
        $this->settings = array_merge_recursive($this->settings, $settings);
    }

    public function addSynonymsFor(array $for)
    {
        if (count($for)) {
            $this->synonymsFor = array_merge($this->synonymsFor, $for);
        }
    }

    public function addExtraSynonyms(array $synonyms)
    {
        if (count($synonyms)) {
            $this->extraSynonyms = array_merge($this->extraSynonyms, $synonyms);
        }
    }

    public function setStores(callable|int|string|array|null $stores): void
    {
        if (is_array($stores)) {
            $this->stores = $stores;
        } else {
            $this->stores = Rapidez::getStores($stores);
        }
    }

    public function setStore(Store|array $store): void
    {
        $this->stores = [$store];
    }
}
