<?php

namespace Rapidez\Core;

use Illuminate\Support\ServiceProvider;

class RapidezServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/rapidez.php' => config_path('rapidez.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rapidez.php', 'rapidez');
    }
}
