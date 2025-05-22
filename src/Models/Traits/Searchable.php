<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Support\Arr;
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

    public static function getIndexMapping(): array
    {
        $mapping = [];

        $methods = Arr::where(get_class_methods(static::class), fn ($method) => str_starts_with($method, 'indexMapping'));
        foreach ($methods as $method) {
            $mapping = array_merge_recursive($mapping, static::{$method}());
        }

        return $mapping;
    }

    public static function getIndexSettings(): array
    {
        $settings = [];

        $methods = Arr::where(get_class_methods(static::class), fn ($method) => str_starts_with($method, 'indexSettings'));
        foreach ($methods as $method) {
            $settings = array_merge_recursive($settings, static::{$method}());
        }

        return $settings;
    }
}
