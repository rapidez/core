<?php

namespace Rapidez\Core;

class Rapidez
{
    public function config(string $path, $default = null): ?string
    {
        return config('rapidez.models.config')::getCachedByPath($path, $default);
    }

    public function parseWidgetContent($content)
    {
        $vars = $this->getWidgetVars($content);
        if (!isset($vars['type'])) {
            return '';
        }

        $parseClass = config('rapidez.widgets.'.$vars['type']);

        if (!class_exists($parseClass) && !app()->environment('production')) {
            return 'Widget not implemented.';
        }

        $content = (new $parseClass($vars))->render();
        return $content;
    }

    private function getWidgetVars($content)
    {
        $content = trim(strip_tags($content), '{{widget,}}');
        parse_str(implode('"&', explode('" ', $content)), $vars);
        $vars = collect($vars);
        return $vars->map(function($values) {
            return str_replace('"', '', $values);
        });
    }
}
