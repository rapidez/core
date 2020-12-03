<?php

namespace Rapidez\Core;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Rapidez\Core\Commands\IndexProductsCommand;
use Rapidez\Core\Commands\InstallCommand;
use Rapidez\Core\Http\Middleware\DetermineAndSetShop;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\Config;
use Rapidez\Core\ViewComponents\PlaceholderComponent;

class RapidezServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'rapidez');
        Blade::component('placeholder', PlaceholderComponent::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/rapidez.php' => config_path('rapidez.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/rapidez'),
            ], 'views');

            $this->commands([
                IndexProductsCommand::class,
                InstallCommand::class,
            ]);
        }

        if (config('rapidez.routes')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }

        $this->app->make(Kernel::class)->pushMiddleware(DetermineAndSetShop::class);

        View::composer('rapidez::layouts.app', function ($view) {
            $exposedFrontendConfigValues = Arr::only(
                config('rapidez'),
                array_merge(config('rapidez.exposed'), ['store_code'])
            );

            config(['frontend' => array_merge(
                config('frontend') ?: [],
                $exposedFrontendConfigValues
            )]);

            config(['frontend.locale' => Config::getCachedByPath('general/locale/code', 'en_US')]);
            config(['frontend.currency' => Config::getCachedByPath('currency/options/default')]);

            config(['frontend.searchable' => Arr::pluck(Attribute::getCachedWhere(function ($attribute) {
                return $attribute['search'];
            }), 'code')]);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rapidez.php', 'rapidez');
    }
}
