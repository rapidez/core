<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;

class Widget extends Model
{
    protected $table = 'widget_instance';

    protected $primaryKey = 'instance_id';

    protected $casts = [
        'widget_parameters' => 'object',
    ];

    protected static function booted()
    {
        static::addGlobalScope('with-all-info', function (Builder $builder) {
            $builder->join('widget_instance_page', 'widget_instance_page.instance_id', '=', 'widget_instance.instance_id');
        });
    }

    public function getContentAttribute(): string
    {
        $widget = config('rapidez.widgets.'.$this->instance_type);
        if (!class_exists($widget) && !app()->environment('production')) {
            return '<hr>'.__('The ":type" widget type is not supported.', ['type' => $this->instance_type]).'<hr>';
        }

        return (new $widget($this->widget_parameters->block_id))->render();
    }
}
