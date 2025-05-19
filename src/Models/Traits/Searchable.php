<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Support\Str;
use JeroenG\Explorer\Domain\Analysis\Analysis;
use JeroenG\Explorer\Domain\Analysis\Analyzer\StandardAnalyzer;
use JeroenG\Explorer\Domain\Analysis\Filter\SynonymFilter;
use Laravel\Scout\Searchable as ScoutSearchable;
use Rapidez\Core\Models\SearchSynonym;
use TorMorten\Eventy\Facades\Eventy;

trait Searchable
{
    use ScoutSearchable;

    public static ?string $indexName;

    public static function getIndexName(): string
    {
        return static::$indexName ?? Str::snake(Str::pluralStudly(class_basename(static::class)));
    }

    public function searchableAs(): string
    {
        return implode('_', array_values([
            config('scout.prefix'),
            static::getIndexName(),
            config('rapidez.store'),
        ]));
    }

    public function synonymFields(): array
    {
        return [];
    }

    public function indexMapping(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function toSearchableArray(): array
    {
        return Eventy::filter('index.' . static::getIndexName() . '.data', $this->toArray(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function mappableAs(): array
    {
        $synonymFields = Eventy::filter('index.' . static::getIndexName() . '.synonymfields', $this->synonymFields());

        $mappings = collect($synonymFields)
            ->mapWithKeys(fn($field) => [
                $field => [
                    'type' => 'text',
                    'analyzer' => 'synonym',
                    'fields' => [
                        'keyword' => [
                            'type' => 'keyword',
                            'ignore_above' => 256,
                        ],
                    ],
                ]
            ])
            ->merge($this->indexMapping())
            ->toArray();

        return Eventy::filter('index.' . static::getIndexName() . '.mapping', $mappings);
    }

    /**
     * {@inheritdoc}
     */
    public function indexSettings(): array
    {
        $synonyms = SearchSynonym::whereIn('store_id', [0, config('rapidez.store')])
            ->pluck('synonyms')
            ->toArray();

        $synonymFilter = new SynonymFilter();
        $synonymFilter->setSynonyms($synonyms);
        $filters = Eventy::filter('index.' . static::getIndexName() . '.settings.filters', [$synonymFilter]);

        $synonymAnalyzer = new StandardAnalyzer('synonym');
        $synonymAnalyzer->setFilters(['lowercase', $synonymFilter]);
        $analyzers = Eventy::filter('index.' . static::getIndexName() . '.settings.analyzers', [$synonymAnalyzer]);

        $analysis = new Analysis();

        foreach($filters as $filter) {
            $analysis->addFilter($filter);
        }

        foreach ($analyzers as $analyzer) {
            $analysis->addAnalyzer($analyzer);
        }

        return $analysis->build();
    }
}
