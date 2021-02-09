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
use Rapidez\Core\Http\ViewComposers\ConfigComposer;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\Config;
use Rapidez\Core\ViewComponents\PlaceholderComponent;
use Rapidez\Core\ViewDirectives\WidgetDirective;

class RapidezServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this
            ->bootCommands()
            ->bootPublishables()
            ->bootRoutes()
            ->bootViews()
            ->bootBladeComponents()
            ->bootMiddleware();
    }

    public function register()
    {
        $this
            ->registerConfigs()
            ->registerBindings();
    }

    protected function bootCommands(): self
    {
        $this->commands([
            IndexProductsCommand::class,
            InstallCommand::class,
        ]);

        return $this;
    }

    protected function bootPublishables(): self
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/rapidez.php' => config_path('rapidez.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/rapidez'),
            ], 'views');
        }

        return $this;
    }

    protected function bootRoutes(): self
    {
        if (config('rapidez.routes')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }

        return $this;
    }

    protected function bootViews(): self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'rapidez');

        View::composer('rapidez::layouts.app', ConfigComposer::class);

        View::addExtension('graphql', 'blade');

        return $this;
    }

    protected function bootBladeComponents(): self
    {
        Blade::component('placeholder', PlaceholderComponent::class);

        Blade::directive('widget', function ($expression) {
            return "<?php echo app('widget-directive')->render($expression) ?>";
        });

        Blade::directive('block', function ($expression) {
            return "<?php echo optional(Rapidez\Core\Models\Block::where('identifier', $expression)->first())->content ?>";
        });

        return $this;
    }

    protected function bootMiddleware(): self
    {
        $this->app->make(Kernel::class)->pushMiddleware(DetermineAndSetShop::class);

        return $this;
    }

    protected function registerConfigs(): self
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rapidez.php', 'rapidez');

        return $this;
    }

    protected function registerBindings(): self
    {
        $this->app->bind('widget-directive', WidgetDirective::class);

        return $this;
    }
}
