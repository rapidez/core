<?php

namespace Rapidez\Core\ViewDirectives;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class WidgetDirective
{
    public function render($location, $type, $handle = 'default', $entities = null, $replace = [])
    {
        return Cache::rememberForever(
            'widget.' . md5(serialize(func_get_args())) . '_' . config('rapidez.store'),
            function () use ($location, $type, $handle, $entities, $replace) {
                $html = '';
                $widgetModel = config('rapidez.models.widget');
                $widgets = $widgetModel::whereIn('layout_handle', (array)$handle)
                    ->whereIn('page_group', (array)$type)
                    ->where('block_reference', $location)
                    ->where(function (Builder $query) use ($entities) {
                        $query->where('page_for', 'all')
                            ->when($entities, function (Builder $query, $entities) {
                                $query->orWhere(function (Builder $query) use ($entities) {
                                    $query->where('page_for', 'specific')
                                        ->whereRaw('FIND_IN_SET(?, entities)', $entities);
                                });
                            });
                    });

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
