<?php

namespace Rapidez\Core\ContentVariables;

use Illuminate\Support\Facades\DB;

class CustomVar
{
    private array $cache = [];

    public function __invoke($content): ?string
    {
        return preg_replace_callback('/{{customVar code=(.*?)}}/ms', function (array $matches): string {
            [$full, $code] = $matches;

            return $this->cache[$code] ??= DB::table('variable')
                ->leftJoin('variable_value', 'variable_value.variable_id', '=', 'variable.variable_id')
                ->whereIn('store_id', [0, config('rapidez.store')])
                ->where('code', $code)
                ->orderByDesc('store_id')
                ->first()->html_value ?? '';
        }, (string) $content);
    }
}
