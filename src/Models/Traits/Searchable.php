<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Support\Str;
use Laravel\Scout\Searchable as ScoutSearchable;
use TorMorten\Eventy\Facades\Eventy;

trait Searchable
{
    use ScoutSearchable;

    public ?string $indexName;

    /**
     * {@inheritdoc}
     */
    public function toSearchableArray(): array
    {
        // TODO: Maybe use the model name here?
        return Eventy::filter('index.' . $this->getIndexName() . '.data', $this->toArray(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexName(): string
    {
        return $this->indexName ?? Str::snake(Str::pluralStudly(class_basename(static::class)));
    }

    public static function indexName(): string
    {
        // @phpstan-ignore-next-line
        return (new static)->getIndexName();
    }

    public function searchableAs(): string
    {
        return implode('_', array_values([
            config('scout.prefix'),
            $this->getIndexName(),
            config('rapidez.store'),
        ]));
    }

    public static function getIndexMappings(): ?array
    {
        return Eventy::filter('index.' . static::indexName() . '.mapping', null);
    }

    public static function getIndexSettings(): ?array
    {
        return Eventy::filter('index.' . static::indexName() . '.settings', null);
    }
}
