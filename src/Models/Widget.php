<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Models\Model;

class Widget extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'widget_instance';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'instance_id';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'widget_parameters' => 'object',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('with-all-info', function (Builder $builder) {
            $builder->join('widget_instance_page', 'widget_instance_page.instance_id', '=', 'widget_instance.instance_id');
        });
    }

    public function getContentAttribute(): string
    {
        switch ($this->instance_type) {
            case 'Magento\Cms\Block\Widget\Block':
                return Block::find($this->widget_parameters->block_id)->content;
        }

        return '<hr>'.__('The ":type" widget type is not supported.', ['type' => $this->instance_type]).'<hr>';
    }
}
