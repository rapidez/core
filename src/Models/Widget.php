<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use TorMorten\Eventy\Facades\Eventy;

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
}
