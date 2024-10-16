<?php

namespace Rapidez\Core;

use Illuminate\Routing\RouteAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Rapidez\Core\Exceptions\StoreNotFoundException;
use Rapidez\Core\Models\Store;

class Rapidez
{
    protected string|bool|null $compadreVersion;

    /** @param Collection<int, array<string, mixed>> $routes */
    public function __construct(protected Collection $routes) {}

    public function addFallbackRoute(mixed $action, int $position = 9999): static
    {
        $this->routes->push([
            'action'   => RouteAction::parse('', $action),
            'position' => $position,
        ]);

        return $this;
    }

    public function removeFallbackRoute(mixed $action): static
    {
        $action = RouteAction::parse('', $action);
        $this->routes = $this->routes->reject(fn ($route) => $route['action'] === $action);

        return $this;
    }

    /** @return Collection<int, array<string, mixed>> */
    public function getAllFallbackRoutes(): Collection
    {
        return $this->routes->sortBy('position');
    }

    public function config(string $path, ?string $default = null, bool $sensitive = false): ?string
    {
        return config('rapidez.models.config')::getCachedByPath($path, $default, $sensitive);
    }

    public function content(string $content): string
    {
        foreach (config('rapidez.frontend.content_variables') as $parser) {
            $parserObj = new $parser;
            if (! is_callable($parserObj)) {
                continue;
            }

            $content = $parserObj($content);
        }

        return $content;
    }

    public function fancyMagentoSyntaxDecoder(string $encodedString): object
    {
        $mapping = [
            '{'  => '^[',
            '}'  => '^]',
            '"'  => '`',
            '\\' => '|',
            '<'  => '^(',
            '>'  => '^)',
        ];

        return json_decode(str_replace(array_values($mapping), array_keys($mapping), $encodedString));
    }

    /** @return array<mixed> */
    public function getStores(callable|int|string|null $store = null): array
    {
        $storeModel = config('rapidez.models.store');

        if ($store) {
            return Arr::where($storeModel::getCached(),
                fn ($s) => is_callable($store)
                    ? $store($s)
                    : $s['store_id'] == $store || $s['code'] == $store
            );
        }

        return $storeModel::getCached();
    }

    /** @return array<string, mixed> */
    public function getStore(callable|int|string $store): array
    {
        $stores = $this->getStores($store);

        throw_if(
            empty($stores),
            StoreNotFoundException::class,
            'Store not found.'
        );

        return Arr::first($stores);
    }

    /** @param Store|array<string, mixed>|callable|int|string $store */
    public function setStore(Store|array|callable|int|string $store): void
    {
        if (is_callable($store) || is_int($store) || is_string($store)) {
            $store = $this->getStore($store);
        } else {
            $store = $this->getStore($store['store_id']);
        }

        config()->set('rapidez.store', $store['store_id']);
        config()->set('rapidez.store_code', $store['code']);
        config()->set('rapidez.website', $store['website_id']);
        config()->set('rapidez.website_code', $store['website_code']);
        config()->set('rapidez.group', $store['group_id']);
        config()->set('rapidez.root_category_id', $store['root_category_id']);
        config()->set('frontend.base_url', url('/'));

        App::setLocale(strtok(Rapidez::config('general/locale/code', 'en_US') ?? 'en_US', '_') ?: 'en');

        Event::dispatch('rapidez:store-set', [$store]);
    }

    /** @param Store|array<string, mixed>|callable|int|string $store */
    public function withStore(Store|array|callable|int|string $store, callable $callback, mixed ...$args): mixed
    {
        $initialStore = config('rapidez.store');
        Rapidez::setStore($store);
        try {
            $result = $callback(...$args);
        } finally {
            Rapidez::setStore($initialStore);
        }

        return $result;
    }

    public function checkCompadreVersion(string $version = '0.0.1', string $operator = '>='): bool
    {
        $this->compadreVersion ??= (DB::table('setup_module')->where('module', 'Rapidez_Compadre')->value('schema_version') ?? false);

        if (! $this->compadreVersion) {
            return false;
        }

        return version_compare($this->compadreVersion, $version, $operator);
    }
}
