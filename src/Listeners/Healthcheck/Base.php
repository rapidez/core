<?php

namespace Rapidez\Core\Listeners\Healthcheck;

use Illuminate\Support\Facades\Event;

class Base
{
    /** @return array<string, mixed> */
    public function handle(): array
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

    public static function register(): void
    {
        Event::listen('rapidez:health-check', static::class);
    }
}
