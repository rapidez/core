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
    ) {
        if (! $stores) {
            $this->setStores(null);
        }
    }

    public function addMapping(string $path, mixed $mapping, bool $overwrite = false): static
    {
        data_set($this->mapping, $path, $mapping, $overwrite);

        return $this;
    }

    public function addMappings(array $mapping): static
    {
        $this->mapping = array_merge_recursive($this->mapping, $mapping);

        return $this;
    }

    public function addSetting(string $path, mixed $setting, bool $overwrite = false): static
    {
        data_set($this->settings, $path, $setting, $overwrite);

        return $this;
    }

    public function addSettings(array $settings): static
    {
        $this->settings = array_merge_recursive($this->settings, $settings);

        return $this;
    }

    public function addSynonymsFor(array $for): static
    {
        if (count($for)) {
            $this->synonymsFor = array_merge($this->synonymsFor, $for);
        }

        return $this;
    }

    public function addExtraSynonyms(array $synonyms): static
    {
        if (count($synonyms)) {
            $this->extraSynonyms = array_merge($this->extraSynonyms, $synonyms);
        }

        return $this;
    }

    public function setStores(callable|int|string|array|null $stores): static
    {
        if (is_array($stores)) {
            $this->stores = $stores;
        } else {
            $this->stores = Rapidez::getStores($stores);
        }

        return $this;
    }

    public function setStore(Store|array $store): static
    {
        $this->stores = [$store];

        return $this;
    }
}
