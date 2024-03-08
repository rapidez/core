<?php

namespace Rapidez\Core\Models;

class QuoteIdMask extends Model
{
    protected $table = 'quote_id_mask';

    protected $primaryKey = 'entity_id';

    public $timestamps = false;

    public function quote()
    {
        return $this->belongsTo(config('rapidez.models.quote'), 'entity_id', 'quote_id');
    }
}
