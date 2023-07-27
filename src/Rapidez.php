<?php

namespace Rapidez\Core;

use Illuminate\Routing\RouteAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Rapidez\Core\Models\Store;

class Rapidez
{
    public function __construct(protected Collection $routes)
    {
    }

    public function addFallbackRoute($action, $position = 9999)
    {
        $this->routes->push([
            'action'   => RouteAction::parse('', $action),
            'position' => $position,
        ]);

        return $this;
    }

    public function removeFallbackRoute($action)
    {
        $action = RouteAction::parse('', $action);
        $this->routes = $this->routes->reject(fn ($route) => $route['action'] === $action);

        return $this;
    }

    public function getAllFallbackRoutes()
    {
        return $this->routes->sortBy('position');
    }

    public function config(string $path, $default = null, bool $sensitive = false): ?string
    {
        return config('rapidez.models.config')::getCachedByPath($path, $default, $sensitive);
    }

    public function content($content)
    {
        foreach (config('rapidez.content-variables') as $parser) {
            $content = (new $parser)($content);
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

    public function getStores(callable|int|string $store = null): array
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

    public function getStore(callable|int|string $store): array
    {
        return Arr::first($this->getStores($store));
    }

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
    }

    public function withStore(Store|array|callable|int|string $store, callable $callback, ...$args)
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
}
