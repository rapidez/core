<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Support\Str;

trait HasContentAttributeWithVariables
{
    public function getContentAttribute(string $content): string
    {
        foreach (get_class_methods($this) as $method) {
            if (Str::startsWith($method, 'process')) {
                $content = $this->$method($content);
            }
        }

        return $content;
    }

    protected function processMediaeUrl(string $content): string
    {
        return preg_replace('/{{media url=("|&quot;|\')(.*?)("|&quot;|\')}}/m', config('rapidez.media_url') . '/${2}', $content);
    }

    protected function processStoreUrl(string $content): string
    {
        return preg_replace('/{{store (url|direct_url)=("|&quot;|\')(.*?)("|&quot;|\')}}/m', '/${3}', $content);
    }

    protected function processWidgets(string $content): string
    {
        return preg_replace_callback('/{{widget type="(.*?)" (.*?)}}/m', function ($matches) {
            [$full, $type, $parameters] = $matches;
            preg_match_all('/(.*?)="(.*?)"/m', $parameters, $parameters, PREG_SET_ORDER);
            foreach ($parameters as $parameter) {
                [$full, $parameter, $value] = $parameter;
                $options[trim($parameter)] = trim($value);
            }

            switch ($type) {
                default:
                    return '<hr>'.__('The ":type" widget type is not supported.', ['type' => $type]).'<hr>';
            }
        }, $content);
    }
}
