<?php

namespace Rapidez\Core\Models;

class QuoteItem extends Model
{
    protected $table = 'quote_item';

    protected $primaryKey = 'item_id';

    public function store()
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    public function quote()
    {
        return $this->belongsTo(config('rapidez.models.quote'));
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_item_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_item_id');
    }

    public function options()
    {
        return $this->hasMany(config('rapidez.models.quote_item_option'), 'item_id');
    }
}
