<?php

namespace Rapidez\Core\Index;

use Illuminate\Support\Arr;

class WithSynonyms
{
    public function __construct(public ?array $fields) {}

    public function getSettings(): array
    {
        $synonyms = config('rapidez.models.search_synonym')::whereIn('store_id', [0, config('rapidez.store')])
            ->get()
            ->map(fn ($synonym) => $synonym->synonyms)
            ->toArray();

        return [
            'index' => [
                'analysis' => [
                    'filter' => [
                        'synonym' => [
                            'type'     => 'synonym_graph',
                            'synonyms' => $synonyms,
                        ],
                    ],
                    'analyzer' => [
                        'default' => [
                            'filter'    => ['lowercase', 'asciifolding'],
                            'tokenizer' => 'standard',
                        ],
                        'synonym' => [
                            'filter'    => ['lowercase', 'asciifolding', 'synonym'],
                            'tokenizer' => 'standard',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getMapping(): array
    {
        if (! count($this->fields ?? [])) {
            return [];
        }

        return [
            'properties' => Arr::mapWithKeys($this->fields, fn ($field) => [
                $field => [
                    'type'     => 'text',
                    'analyzer' => 'synonym',
                    'fields'   => [
                        'keyword' => [
                            'type'         => 'keyword',
                            'ignore_above' => 256,
                        ],
                    ],
                ],
            ]),
        ];
    }
}
