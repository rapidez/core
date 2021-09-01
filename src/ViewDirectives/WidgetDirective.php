<?php

namespace Rapidez\Core\ViewDirectives;

use Illuminate\Support\Facades\Cache;

class WidgetDirective
{
    public function render($location, $type, $handle = 'default', $entities = null, $replace = [])
    {
        return Cache::rememberForever(
            'widget.'.md5(serialize(func_get_args())),
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
                    $widgetClass = config('rapidez.widgets.'.$widget->instance_type);

                    if (!class_exists($widgetClass)) {
                        if (!app()->environment('production')) {
                            $html .= '<hr>'.__('Widget not implemented (:type).', ['type' => $widget->instance_type]).'<hr>';
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
