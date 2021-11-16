<?php

namespace Rapidez\Core\ContentVariables;

class Widget
{
    public function __invoke($content)
    {
        return preg_replace_callback('/{{widget type="(.*?)" (.*?)}}/m', function ($matches) {
            [$full, $type, $parameters] = $matches;
            preg_match_all('/(.*?)="(.*?)"/m', $parameters, $parameters, PREG_SET_ORDER);
            foreach ($parameters as $parameter) {
                [$full, $parameter, $value] = $parameter;
                $options[trim($parameter)] = trim($value);
            }

            if (!isset($type)) {
                return '';
            }

            $widgetClass = config('rapidez.widgets.'.$type);

            if (!class_exists($widgetClass)) {
                return !app()->environment('production')
                    ? '<hr>'.__('Widget not implemented (:type).', compact('type')).'<hr>'
                    : '';
            }

            return (new $widgetClass((object) $options))->render();
        }, $content);
    }
}
