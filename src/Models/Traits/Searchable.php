<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Support\Str;
use Laravel\Scout\Searchable as ScoutSearchable;
use TorMorten\Eventy\Facades\Eventy;

trait Searchable
{
    use ScoutSearchable;

    public static ?string $indexName;

    /**
     * {@inheritdoc}
     */
    public function toSearchableArray(): array
    {
        // TODO: Maybe use the model name here?
        return Eventy::filter('index.' . static::getIndexName() . '.data', $this->toArray(), $this);
    }

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

    public static function getIndexMappings(): ?array
    {
        return Eventy::filter('index.' . static::getIndexName() . '.mapping', null);
    }

    public static function getIndexSettings(): ?array
    {
        return Eventy::filter('index.' . static::getIndexName() . '.settings', null);
    }
}
