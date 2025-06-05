<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable as ScoutSearchable;
use Rapidez\Core\Index\WithSynonyms;
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
        return Eventy::filter('index.' . static::getEventyName() . '.data', $this->toArray(), $this);
    }

    public abstract static function getEventyName(): string;

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

    public static function synonymFields(): array
    {
        return [];
    }

    protected static function indexMapping(): array
    {
        return [];
    }

    protected static function indexSettings(): array
    {
        return [];
    }

    public static function getIndexMapping(): array
    {
        $synonymFields = Eventy::filter('index.' . static::getEventyName() . '.synonym-fields', static::synonymFields());

        return static::filter('mapping', [...static::indexMapping(), [WithSynonyms::class, 'fields' => $synonymFields]]);
    }

    public static function getIndexSettings(): array
    {
        return static::filter('settings', [...static::indexSettings(), WithSynonyms::class]);
    }

    private static function filter($type, $initialValue): array
    {
        [$data, $classes] = Arr::partition(
            Eventy::filter('index.' . static::getEventyName() . '.' . $type, $initialValue),
            fn ($value, $key) => is_string($key)
        );

        // Execute all the classes and merge their output with the data
        collect($classes)
            ->map(Arr::wrap(...))
            ->filter(fn ($class) => count($class))
            ->each(function ($class) use ($type, &$data) {
                $object = app($class[0], array_slice($class, 1));
                $function = 'get' . ucfirst($type);
                if (method_exists($object, $function)) {
                    $data = array_merge_recursive($data, $object->{$function}());
                }
            });

        return $data;
    }
}
