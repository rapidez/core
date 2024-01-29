<?php

namespace Rapidez\Core;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Rapidez\Core\Auth\MagentoCustomerTokenGuard;
use Rapidez\Core\Commands\IndexCategoriesCommand;
use Rapidez\Core\Commands\IndexProductsCommand;
use Rapidez\Core\Commands\InstallCommand;
use Rapidez\Core\Commands\InstallTestsCommand;
use Rapidez\Core\Commands\ValidateCommand;
use Rapidez\Core\Events\IndexBeforeEvent;
use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Facades\Rapidez as RapidezFacade;
use Rapidez\Core\Http\Controllers\Fallback\CmsPageController;
use Rapidez\Core\Http\Controllers\Fallback\LegacyFallbackController;
use Rapidez\Core\Http\Controllers\Fallback\UrlRewriteController;
use Rapidez\Core\Http\Middleware\CheckStoreCode;
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
    protected $configFiles = [
        'frontend',
        'healthcheck',
        'indexer',
        'jwt',
        'models',
        'routing',
        'system',
    ];

    public function boot()
    {
        $this
            ->bootAuth()
            ->bootCommands()
            ->bootPublishables()
            ->bootRoutes()
            ->bootViews()
            ->bootBladeComponents()
            ->bootMiddleware()
            ->bootTranslations()
            ->bootListeners()
            ->bootMacros();
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

    protected function bootAuth(): self
    {
        auth()->extend('magento-customer', function (Application $app, string $name, array $config) {
            return new MagentoCustomerTokenGuard(auth()->createUserProvider($config['provider']), request(), 'token', 'token');
        });

        config([
            'auth.guards.magento-customer' => [
                'driver'   => 'magento-customer',
                'provider' => 'users',
            ],
        ]);

        return $this;
    }

    protected function bootCommands(): self
    {
        $this->commands([
            IndexProductsCommand::class,
            IndexCategoriesCommand::class,
            ValidateCommand::class,
            InstallCommand::class,
            InstallTestsCommand::class,
        ]);

        Event::listen(IndexBeforeEvent::class, function ($event) {
            $event->context->call('rapidez:index:categories');
        });

        return $this;
    }

    protected function bootPublishables(): self
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/rapidez.php' => config_path('rapidez.php'),
            ], 'config');

            foreach ($this->configFiles as $configFile) {
                $this->publishes([
                    __DIR__ . '/../config/rapidez/' . $configFile . '.php' => config_path('rapidez/' . $configFile . '.php'),
                ], 'config');
            }

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/rapidez'),
            ], 'views');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/rapidez'),
            ], 'translations');

            $this->publishes([
                __DIR__ . '/../resources/payment-icons' => public_path('payment-icons'),
            ], 'payment-icons');
        }

        return $this;
    }

    protected function bootRoutes(): self
    {
        if (config('rapidez.routing.enabled')) {
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
        if (app()->runningInConsole()) {
            return $this;
        }

        $path = config('rapidez.frontend.themes.' . request()->server('MAGE_RUN_CODE', request()->has('_store') && ! app()->isProduction() ? request()->get('_store') : 'default'), false);

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

        Vite::useScriptTagAttributes(fn (string $src, string $url, ?array $chunk, ?array $manifest) => [
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

        Blade::if('storecode', function ($value) {
            $value = is_array($value) ? $value : func_get_args();

            return in_array(config('rapidez.store_code'), $value);
        });

        return $this;
    }

    protected function bootMiddleware(): self
    {
        $this->app->make(Kernel::class)->pushMiddleware(DetermineAndSetShop::class);

        $this->app['router']->aliasMiddleware('store_code', CheckStoreCode::class);

        return $this;
    }

    protected function bootTranslations(): self
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'rapidez');

        return $this;
    }

    protected function bootListeners(): self
    {
        Event::listen(ProductViewEvent::class, ReportProductView::class);
        MagentoSettingsHealthcheck::register();
        ElasticsearchHealthcheck::register();

        return $this;
    }

    protected function bootMacros(): self
    {
        Collection::macro('firstForCurrentStore', function () {
            return $this->filter(function ($value) {
                return in_array($value->store_id, [config('rapidez.store'), 0]);
            })->sortByDesc('store_id')->first();
        });

        return $this;
    }

    protected function registerConfigs(): self
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rapidez.php', 'rapidez');
        foreach ($this->configFiles as $configFile) {
            $this->mergeConfigFrom(__DIR__ . '/../config/rapidez/' . $configFile . '.php', 'rapidez.' . $configFile);
        }

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
