<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Support\Arr;

trait UsesSynonyms
{
    public abstract static function getSynonymFields(): array;

    public static function indexSettingsSynonyms(): array
    {
        $fields = static::getSynonymFields();

        if (! count($fields)) {
            return [];
        }

        $synonyms = config('rapidez.models.search_synonym')::whereIn('store_id', [0, config('rapidez.store')])
            ->get()
            ->map(fn ($synonym) => $synonym->synonyms)
            ->toArray();

        return [
            'index' => [
                'analysis' => [
                    'filter' => [
                        'synonym' => [
                            'type' => 'synonym_graph',
                            'synonyms' => $synonyms,
                        ],
                    ],
                    'analyzer' => [
                        'default' => [
                            'filter' => ['lowercase', 'asciifolding'],
                            'tokenizer' => 'standard',
                        ],
                        'synonym' => [
                            'filter' => ['lowercase', 'asciifolding', 'synonym'],
                            'tokenizer' => 'standard',
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function indexMappingSynonyms(): array
    {
        $fields = static::getSynonymFields();

        if (! count($fields)) {
            return [];
        }

        return [
            'properties' => Arr::mapWithKeys($fields, fn($field) => [
                $field => [
                    'type' => 'text',
                    'analyzer' => 'synonym',
                    'fields' => [
                        'keyword' => [
                            'type' => 'keyword',
                            'ignore_above' => 256,
                        ],
                    ],
                ],
            ])
        ];
    }
}
