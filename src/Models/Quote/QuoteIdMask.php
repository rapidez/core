<?php

namespace Rapidez\Core\Models\Quote;

use Illuminate\Database\Eloquent\Model;

class QuoteIdMask extends Model
{
    protected $table = 'quote_id_mask';

    public $timestamps = false;

    protected $casts = [
        'quote_id' => 'int',
    ];

    public function quote()
    {
        return $this->belongsTo(config('rapidez.models.quote'));
    }
}
