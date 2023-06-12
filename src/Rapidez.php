<?php

namespace Rapidez\Core;

use Illuminate\Routing\RouteAction;
use Illuminate\Support\Collection;

class Rapidez
{
    public function __construct(protected Collection $routes)
    {
    }

    public function addFallbackRoute($action, $position = 9999)
    {
        $this->routes->push([
            'action'   => RouteAction::parse('', $action),
            'position' => $position,
        ]);

        return $this;
    }

    public function removeFallbackRoute($action)
    {
        $action = RouteAction::parse('', $action);
        $this->routes = $this->routes->reject(fn ($route) => $route['action'] === $action);

        return $this;
    }

    public function getAllFallbackRoutes()
    {
        return $this->routes->sortBy('position');
    }

    public function config(string $path, $default = null, bool $sensitive = false): ?string
    {
        return config('rapidez.models.config')::getCachedByPath($path, $default, $sensitive);
    }

    public function content($content)
    {
        foreach (config('rapidez.content-variables') as $parser) {
            $content = (new $parser())($content);
        }

        return $content;
    }

    public function fancyMagentoSyntaxDecoder(string $encodedString): object
    {
        $mapping = [
            '{'  => '^[',
            '}'  => '^]',
            '"'  => '`',
            '\\' => '|',
            '<'  => '^(',
            '>'  => '^)',
        ];

        return json_decode(str_replace(array_values($mapping), array_keys($mapping), $encodedString));
    }
}
