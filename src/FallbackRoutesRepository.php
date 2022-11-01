<?php

namespace Rapidez\Core;

use Illuminate\Routing\RouteAction;
use Illuminate\Support\Collection;

class FallbackRoutesRepository
{
    public function __construct(protected Collection $routes)
    {
    }

    public function add($action, $position = 99999)
    {
        $this->routes->push([
            'action'   => RouteAction::parse('', $action),
            'position' => $position,
        ]);

        return $this;
    }

    public function all()
    {
        return $this->routes->sortBy('position');
    }
}
