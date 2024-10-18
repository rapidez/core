<?php

namespace Rapidez\Core\ContentVariables;

class Widget
{
    /** @return array<int, string>|string|null */
    public function __invoke(string $content): array|string|null
    {
        return preg_replace_callback('/{{widget type="(.*?)" (.*?)}}/ms', function ($matches) {
            [$full, $type, $parameters] = $matches;
            preg_match_all('/(.*?)="(.*?)"/ms', $parameters, $parameters, PREG_SET_ORDER);

            $options = [];
            foreach ($parameters as $parameter) {
                [$full, $parameter, $value] = $parameter;
                $options[trim($parameter)] = trim($value);
            }

            if (! $type) {
                return '';
            }

            $widgetClass = config('rapidez.frontend.widgets.' . $type);

            if (class_exists($widgetClass)) {
                return (new $widgetClass((object) $options))->render();
            }

            if (is_null($widgetClass)) {
                return ! app()->environment('production')
                    ? '<hr>' . __('Widget not implemented (:type).', compact('type')) . '<hr>'
                    : '';
            }

            // When the widgetClass is not a class instance we handle it as a view name
            $viewName = $widgetClass;
            $widgetClass = config('rapidez.frontend.view_only_widget');

            return (new $widgetClass((object) $options))->render($viewName);
        }, $content);
    }
}
