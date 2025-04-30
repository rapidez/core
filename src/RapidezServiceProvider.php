<?php

namespace Rapidez\Core;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as ViewComponent;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Rapidez\Core\Auth\MagentoCartTokenGuard;
use Rapidez\Core\Auth\MagentoCustomerTokenGuard;
use Rapidez\Core\Commands\IndexCommand;
use Rapidez\Core\Commands\InstallCommand;
use Rapidez\Core\Commands\InstallTestsCommand;
use Rapidez\Core\Commands\UpdateIndexCommand;
use Rapidez\Core\Commands\ValidateCommand;
use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Facades\Rapidez as RapidezFacade;
use Rapidez\Core\Http\Controllers\Fallback\CmsPageController;
use Rapidez\Core\Http\Controllers\Fallback\LegacyFallbackController;
use Rapidez\Core\Http\Controllers\Fallback\UrlRewriteController;
use Rapidez\Core\Http\Middleware\CheckStoreCode;
use Rapidez\Core\Http\Middleware\DetermineAndSetShop;
use Rapidez\Core\Listeners\Healthcheck\ElasticsearchHealthcheck;
use Rapidez\Core\Listeners\Healthcheck\MagentoSettingsHealthcheck;
use Rapidez\Core\Listeners\Healthcheck\ModelsHealthcheck;
use Rapidez\Core\Listeners\ReportProductView;
use Rapidez\Core\Listeners\UpdateLatestIndexDate;
use Rapidez\Core\ViewComponents\PlaceholderComponent;
use Rapidez\Core\ViewDirectives\WidgetDirective;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RapidezServiceProvider extends ServiceProvider
{
    protected $configFiles = [
        'frontend',
        'healthcheck',
        'jwt',
        'magento-defaults',
        'models',
        'routing',
        'searchkit',
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
            ->registerExceptionHandlers()
            ->registerBladeIconConfig();
    }

    protected function bootAuth(): self
    {
        MagentoCustomerTokenGuard::register();
        MagentoCartTokenGuard::register();

        return $this;
    }

    protected function bootCommands(): self
    {
        $this->commands([
            IndexCommand::class,
            UpdateIndexCommand::class,
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

            foreach ($this->configFiles as $configFile) {
                $this->publishes([
                    __DIR__ . '/../config/rapidez/' . $configFile . '.php' => config_path('rapidez/' . $configFile . '.php'),
                ], 'config');
            }

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/rapidez'),
            ], 'views');

            // We're so explicit here as otherwise the json translations will also be published.
            // That will publish to /lang/vendor/rapidez/nl.json where it will not be loaded
            // by default and; you should keep everyting in one place: /lang/nl.json
            foreach (['en', 'nl'] as $lang) {
                $this->publishes([
                    __DIR__ . '/../lang/' . $lang . '/frontend.php' => lang_path('vendor/rapidez/' . $lang . '/frontend.php'),
                ], 'rapidez-translations');
            }

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

        if (! app()->runningInConsole() && config('rapidez.routing.earlyhints.enabled', true)) {
            $this->app->call(function (\Illuminate\Contracts\Http\Kernel $kernel) {
                /** @var \Illuminate\Foundation\Http\Kernel $kernel */
                $middlewares = $kernel->getGlobalMiddleware();
                $middlewares[] = \JustBetter\Http3EarlyHints\Middleware\AddHttp3EarlyHints::class;

                $kernel->setGlobalMiddleware($middlewares);
            });
        }

        return $this;
    }

    protected function bootViews(): self
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'rapidez');

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
            return "<?php echo Rapidez::config({$expression}) ?>";
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
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'rapidez');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../lang');

        return $this;
    }

    protected function bootListeners(): self
    {
        Event::listen(ProductViewEvent::class, ReportProductView::class);
        ModelsHealthcheck::register();
        MagentoSettingsHealthcheck::register();
        ElasticsearchHealthcheck::register();
        UpdateLatestIndexDate::register();

        return $this;
    }

    protected function bootMacros(): self
    {
        Collection::macro('firstForCurrentStore', function () {
            /** @var Collection $this */
            return $this->filter(function ($value) {
                return in_array($value->store_id, [config('rapidez.store'), 0]);
            })->sortByDesc('store_id')->first();
        });

        ViewComponent::macro('renderOneliner', function () {
            /** @var ViewComponent $this */
            return Str::of($this->render())
                ->replaceMatches('/#.*/m', '')
                ->squish();
        });

        Vite::macro('getPathsByFilenames', function ($filenames) {
            /** @var \Illuminate\Foundation\Vite $this */
            $filenames = is_array($filenames) ? $filenames : func_get_args();
            $manifest = $this->manifest($this->buildDirectory); // @phpstan-ignore-line False positive, the macro bind allows us to access protected properties.

            return array_filter(
                array_map(
                    function ($filename) use ($manifest) {
                        foreach ($manifest as $path => $asset) {
                            if (Str::endsWith($asset['name'] ?? '', $filename) || Str::endsWith($path, $filename)) {
                                return $path;
                            }
                        }
                    },
                    $filenames
                )
            );
        });

        return $this;
    }

    protected function registerConfigs(): self
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rapidez.php', 'rapidez');
        foreach ($this->configFiles as $configFile) {
            $this->mergeConfigFrom(__DIR__ . '/../config/rapidez/' . $configFile . '.php', 'rapidez.' . $configFile);
        }

        if (! config('cache.stores.rapidez:multi', false)) {
            $fallbackDriver = config('cache.default');
            if ($fallbackDriver === 'rapidez:multi') {
                $fallbackDriver = config('cache.multi-fallback', 'file');
                Log::warning('Default cache driver is rapidez:multi, setting fallback driver to ' . $fallbackDriver);
            }

            config()->set('cache.stores.rapidez:multi', [
                'driver' => 'multi',
                'stores' => [
                    'array',
                    $fallbackDriver,
                ],
                'sync_missed_stores' => true,
            ]);
        }

        return $this;
    }

    protected function registerThemes(): self
    {
        if (app()->runningInConsole()) {
            return $this;
        }

        $path = config('rapidez.frontend.theme', false);

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

    protected function registerBladeIconConfig(): self
    {
        config()->set('blade-icons.attributes.defer', config('blade-icons.attributes.defer', true));

        return $this;
    }
}
