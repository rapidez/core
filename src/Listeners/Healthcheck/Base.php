<?php

namespace Rapidez\Core\Listeners\Healthcheck;

use Illuminate\Support\Facades\Event;

class Base
{
    public function handle()
    {
        return [
            'healthy'  => true,
            'messages' => [
                // [
                //     'type'  => 'error',
                //     'value' => ''.
                // ],
                // [
                //     'type'  => 'warn',
                //     'value' => ''.
                // ],
                // [
                //     'type'  => 'info',
                //     'value' => ''.
                // ],
            ],
        ];
    }

    public static function register()
    {
        Event::listen('rapidez:health-check', static::class);
    }
}
