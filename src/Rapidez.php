<?php

namespace Rapidez\Core;

class Rapidez
{
    public function config(string $path, $default = null, bool $sensitive = false, ?int $storeId = null): ?string
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
