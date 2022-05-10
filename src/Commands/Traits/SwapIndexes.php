<?php

namespace Rapidez\Core\Commands\Traits;

trait SwapIndexes
{
    /**
     * Creating index based on name and optional mappings.
     * @param  string $index
     * @param  array $mapping = []
     * @return void
     */
    public function createIndex(string $index, array $mapping = []): void
    {
        $this->elasticsearch->indices()->create([
            'index' => $index,
            'body' => [
                'mappings' => $mapping
            ]
        ]);
    }

    /**
     * Switch alias to new index and delete old index.
     * @param  string $alias
     * @param  string $index
     * @return void
     */
    public function switchAlias(string $alias, string $index): void
    {
        $this->elasticsearch->indices()->putAlias([
            'index' => $index,
            'name'  => $alias,
        ]);

        $aliases = $this->elasticsearch->indices()->getAlias(['name' => $alias]);
        foreach ($aliases as $indexLinkedToAlias => $aliasData) {
            if ($indexLinkedToAlias != $index) {
                $this->elasticsearch->indices()->deleteAlias([
                    'index' => $indexLinkedToAlias,
                    'name'  => $alias,
                ]);
                $this->elasticsearch->indices()->delete(['index' => $indexLinkedToAlias]);
            }
        }
    }
}
