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

    protected static function booting()
    {
        static::addGlobalScope('with-all-info', function (Builder $builder) {
            $builder->join('widget_instance_page', 'widget_instance_page.instance_id', '=', 'widget_instance.instance_id');
        });
        static::addGlobalScope('for-current-store', function (Builder $builder) {
            $builder->whereRaw('? IN (store_ids)', [0])->orWhereRaw('? IN (store_ids)', [config('rapidez.store')]);
        });
    }
}
