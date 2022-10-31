<?php

namespace Rapidez\Core;

use Illuminate\Routing\RouteAction;

class FallbackRoutesRepository
{
    protected $routes;

    public function __construct()
    {
        $this->routes = collect([]);
    }

    public function add($action, $position = 99999)
    {
        $this->routes->push([
            'action'   => RouteAction::parse('', $action),
            'position' => $position
        ]);

        return $this;
    }

    public function all()
    {
        return $this->routes->sortBy('position');
    }
}
