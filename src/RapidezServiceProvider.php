<?php

namespace Rapidez\Core;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Rapidez\Core\Commands\IndexProductsCommand;
use Rapidez\Core\Commands\InstallCommand;
use Rapidez\Core\Commands\InstallTestsCommand;
use Rapidez\Core\Commands\ValidateCommand;
use Rapidez\Core\Facades\Rapidez as RapidezFacade;
use Rapidez\Core\Http\Controllers\Fallback\CmsPageController;
use Rapidez\Core\Http\Controllers\Fallback\LegacyFallbackController;
use Rapidez\Core\Http\Controllers\Fallback\UrlRewriteController;
use Rapidez\Core\Http\Middleware\DetermineAndSetShop;
use Rapidez\Core\Http\ViewComposers\ConfigComposer;
use Rapidez\Core\Listeners\ElasticsearchHealthcheck;
use Rapidez\Core\Listeners\MagentoSettingsHealthcheck;
use Rapidez\Core\Listeners\ReportProductView;
use Rapidez\Core\ViewComponents\PlaceholderComponent;
use Rapidez\Core\ViewDirectives\WidgetDirective;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RapidezServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this
            ->bootCommands()
            ->bootPublishables()
            ->bootRoutes()
            ->bootViews()
            ->bootMacros()
            ->bootBladeComponents()
            ->bootMiddleware()
            ->bootTranslations()
            ->bootListeners();
    }

    public function bootTranslations(): self
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'rapidez');

        return $this;
    }

    public function register()
    {
        $this
            ->registerConfigs()
            ->registerBindings()
            ->registerThemes()
            ->registerBladeDirectives()
            ->registerExceptionHandlers();
    }

    protected function bootCommands(): self
    {
        $this->commands([
            IndexProductsCommand::class,
            ValidateCommand::class,
            InstallCommand::class,
            InstallTestsCommand::class,
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
                __DIR__ . '/../resources/views' => resource_path('views/vendor/rapidez'),
            ], 'views');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/rapidez'),
            ], 'translations');
        }

        return $this;
    }

    protected function bootRoutes(): self
    {
        if (config('rapidez.routes')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }

        RapidezFacade::addFallbackRoute(UrlRewriteController::class, 5);
        RapidezFacade::addFallbackRoute(CmsPageController::class, 10);
        RapidezFacade::addFallbackRoute(LegacyFallbackController::class, 99999);

        return $this;
    }

    protected function registerThemes(): self
    {
        $path = config('rapidez.themes.' . request()->server('MAGE_RUN_CODE', request()->has('_store') && ! app()->isProduction() ? request()->get('_store') : 'default'), false);

        if (! $path) {
            return $this;
        }

        config([
            'view.paths' => [
                $path,
                ...config('view.paths'),
            ],
        ]);

        return $this;
    }

    protected function bootViews(): self
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'rapidez');

        View::composer('rapidez::layouts.app', ConfigComposer::class);

        View::addExtension('graphql', 'blade');

        Vite::useScriptTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
            'data-turbo-track' => str_contains($url, 'app') ? 'reload' : false,
            'defer'            => true,
        ]);

        Vite::useStyleTagAttributes([
            'data-turbo-track' => 'reload',
        ]);

        return $this;
    }

    protected function bootBladeComponents(): self
    {
        Blade::component('placeholder', PlaceholderComponent::class);

        return $this;
    }

    protected function registerBladeDirectives(): self
    {
        Blade::directive('content', function ($expression) {
            return "<?php echo Rapidez::content({$expression}) ?>";
        });

        Blade::directive('widget', function ($expression) {
            return "<?php echo app('widget-directive')->render({$expression}) ?>";
        });

        Blade::directive('block', function ($expression) {
            $blockModel = config('rapidez.models.block');

            return "<?php echo Rapidez::content({$blockModel}::getCachedByIdentifier({$expression})) ?>";
        });

        Blade::directive('config', function ($expression) {
            $configModel = config('rapidez.models.config');

            return "<?php echo {$configModel}::getCachedByPath({$expression}) ?>";
        });

        return $this;
    }

    protected function bootMacros(): self
    {
        /**
         * @see https://github.com/laravel/framework/pull/47569
         * Check if the componentAttributeBag contains any of the keys.
         * Usage: $attributes->hasAny(['href', ':href', "v-bind:href"])
         */
        ComponentAttributeBag::macro('hasAny', function ($key) {
            /** @var ComponentAttributeBag $this */
            if (! isset($this->attributes)) {
                return false;
            }

            $keys = is_array($key) ? $key : func_get_args();

            foreach ($keys as $value) {
                if ($this->has($value)) {
                    return true;
                }
            }
        });

        return $this;
    }

    protected function bootListeners(): self
    {
        Event::listen(ProductViewEvent::class, ReportProductView::class);
        MagentoSettingsHealthcheck::register();
        ElasticsearchHealthcheck::register();

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
        $this->app->singleton('rapidez', Rapidez::class);
        $this->app->bind('widget-directive', WidgetDirective::class);

        return $this;
    }

    protected function registerExceptionHandlers(): self
    {
        $exceptionHandler = app(ExceptionHandler::class);

        method_exists($exceptionHandler, 'reportable') && $exceptionHandler
            ->reportable(function (RequiredConstraintsViolated $e) {
                return false;
            });

        method_exists($exceptionHandler, 'renderable') && $exceptionHandler
            ->renderable(function (RequiredConstraintsViolated $e, Request $request) {
                throw new HttpException(401, $e->getMessage(), $e);
            });

        return $this;
    }
}
