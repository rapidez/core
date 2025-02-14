<?php

namespace Rapidez\Core;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Rapidez\Core\Exceptions\StoreNotFoundException;
use Rapidez\Core\Models\Config;
use Rapidez\Core\Models\ConfigScopes;
use Rapidez\Core\Models\Store;
use ReflectionClass;

class Rapidez
{
    protected string|bool|null $compadreVersion;

    public function __construct(protected Collection $routes) {}

    public function addFallbackRoute(Route|array|string $action, $position = 9999)
    {
        $this->routes->push([
            'route'    => $this->actionToRoute($action)->fallback(),
            'position' => $position,
        ]);

        return $this;
    }

    private function actionToRoute(Route|array|string $action): Route
    {
        if ($action instanceof Route) {
            return $action;
        }

        $router = new ReflectionClass(Router::class);
        $createRoute = $router->getMethod('createRoute');
        $createRoute->setAccessible(true);

        return $createRoute->invoke(app(Router::class), ['GET'], '', $action);
    }

    public function removeFallbackRoute(Route|array|string $action)
    {
        $action = $this->actionToRoute($action);
        $this->routes = $this->routes->reject(fn ($route) => $route['route']->action === $action->action);

        return $this;
    }

    public function getAllFallbackRoutes()
    {
        return $this->routes->sortBy('position');
    }

    public function config(string $path, $default = null, bool $sensitive = false): ?string
    {
        return config('rapidez.models.config')::getValue($path, options: ['cache' => true, 'decrypt' => $sensitive]) ?? $default;
    }

    public function content($content)
    {
        foreach (config('rapidez.frontend.content_variables') as $parser) {
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

    public function getStores(callable|array|int|string|null $store = null): array
    {
        $storeModel = config('rapidez.models.store');

        if ($store) {
            return Arr::where($storeModel::getCached(),
                fn ($s) => is_callable($store)
                    ? $store($s)
                    : in_array($s['store_id'], Arr::wrap($store)) || in_array($s['code'], Arr::wrap($store))
            );
        }

        return $storeModel::getCached();
    }

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
        config()->set('rapidez.index', implode('_', array_values([
            config('scout.prefix'),
            'products',
            $store['store_id'],
        ])));

        if (config()->get('rapidez.magento_url_from_db', false)) {
            $magentoUrl = trim(
                Config::getValue('web/secure/base_url', ConfigScopes::SCOPE_WEBSITE) ?? config()->get('rapidez.magento_url'),
                '/'
            );

            $storeUrl = trim(
                Config::getValue('web/secure/base_url', ConfigScopes::SCOPE_STORE) ?? config()->get('frontend.base_url'),
                '/'
            );

            // Make sure the store url is not the same as the magentoUrl
            if ($magentoUrl !== $storeUrl) {
                URL::forceRootUrl($storeUrl);
            }

            // Make sure the Magento url is not the Rapidez url before setting it
            if ($magentoUrl !== url('/')) {
                config()->set('rapidez.magento_url', $magentoUrl);
            }

            $mediaUrl = trim(
                str_replace(
                    ['{{secure_base_url}}', '{{unsecure_base_url}}'],
                    config()->get('rapidez.magento_url') . '/',
                    Config::getValue('web/secure/base_media_url', ConfigScopes::SCOPE_WEBSITE) ?? config()->get('rapidez.media_url')
                ),
                '/'
            );
            // Make sure the Magento media url is not the same as Rapidez url before setting it
            if ($mediaUrl !== url('/media')) {
                config()->set('rapidez.media_url', $mediaUrl);
            }
        }

        config()->set('frontend.base_url', url('/'));
        App::setLocale(strtok(Rapidez::config('general/locale/code', 'en_US'), '_'));

        Event::dispatch('rapidez:store-set', [$store]);
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

    public function checkCompadreVersion($version = '0.0.1', $operator = '>=')
    {
        $this->compadreVersion ??= (DB::table('setup_module')->where('module', 'Rapidez_Compadre')->value('schema_version') ?? false);

        if (! $this->compadreVersion) {
            return false;
        }

        return version_compare($this->compadreVersion, $version, $operator);
    }
}
