<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

class HealthcheckController
{
    public function __invoke()
    {
        $result = array_merge_recursive(...Event::dispatch('rapidez:health-check'));
        $isHealthy = Arr::first($result['healthy'], fn ($healthy) => ! $healthy, true);
        $result['healthy'] = $isHealthy;

        return response($result, $isHealthy ? 200 : 503);
    }
}
