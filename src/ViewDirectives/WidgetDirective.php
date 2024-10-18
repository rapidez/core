<?php

namespace Rapidez\Core\ViewDirectives;

use Illuminate\Support\Facades\Cache;

class WidgetDirective
{
    /** @param array<string, string> $replace */
    public function render(?string $location, ?string $type, ?string $handle = 'default', ?string $entities = null, array $replace = []): string
    {
        return Cache::rememberForever(
            'widget.' . md5(serialize(func_get_args())) . '_' . config('rapidez.store'),
            function () use ($location, $type, $handle, $entities, $replace) {
                $html = '';

                if ($type == 'pages') {
                    $type = ['pages', 'all_pages'];
                }

                $widgetModel = config('rapidez.models.widget');
                $widgets = $widgetModel::where('layout_handle', $handle)
                    ->{is_array($type) ? 'whereIn' : 'where'}('page_group', $type)
                    ->where('block_reference', $location);

                if ($entities) {
                    $widgets->where('entities', $entities);
                }

                foreach ($widgets->get() as $widget) {
                    $widgetClass = config('rapidez.frontend.widgets.' . $widget->instance_type);

                    if (! class_exists($widgetClass)) {
                        if (is_null($widgetClass)) {
                            if (! app()->environment('production')) {
                                $html .= '<hr>' . __('Widget not implemented (:type).', ['type' => $widget->instance_type]) . '<hr>';
                            }
                        } else {
                            // When the widgetClass is not a class instance we handle it as a view name
                            $viewName = $widgetClass;
                            $widgetClass = config('rapidez.frontend.view_only_widget');

                            $html .= (new $widgetClass($widget->widget_parameters))->render($viewName);
                        }
                    } else {
                        $html .= (new $widgetClass($widget->widget_parameters))->render();
                    }
                }

                return empty($replace) ? $html : strtr($html, $replace);
            }
        );
    }
}
