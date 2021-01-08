<?php

namespace Rapidez\Core\ViewDirectives;

use Rapidez\Core\Models\Widget;

class WidgetDirective
{
    public function render($location, $type, $handle = 'default', $entities = null)
    {
        $html = '';

        if ($type == 'pages') {
            $type = ['pages', 'all_pages'];
        }

        $widgets = Widget::where('layout_handle', $handle)
            ->{is_array($type) ? 'whereIn' : 'where'}('page_group', $type)
            ->where('block_reference', $location);

        if ($entities) {
            $widgets->where('entities', $entities);
        }

        foreach ($widgets->get() as $widget) {
            $html .= $widget->content;
        }

        return $html;
    }
}
