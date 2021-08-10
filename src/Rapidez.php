<?php

namespace Rapidez\Core;

use Rapidez\Core\Models\Traits\HasContentAttributeWithVariables;

class Rapidez
{
    use HasContentAttributeWithVariables;

    public function config(string $path, $default = null): ?string
    {
        return config('rapidez.models.config')::getCachedByPath($path, $default);
    }

    public function getContent($content)
    {
        return $this->getContentAttribute($content);
    }

    private function getWidgetVars($content)
    {
        $content = trim(strip_tags($content), '{{widget,}}');
        parse_str(implode('"&', explode('" ', $content)), $vars);
        $vars = collect($vars);
        return $vars->map(function ($values) {
            return str_replace('"', '', $values);
        });
    }

    public static function fancyMagentoSyntaxDecoder(string $encodedString): object
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
