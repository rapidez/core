<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteIdMask extends Model
{
    protected $table = 'quote_id_mask';

    protected $primaryKey = 'entity_id';

    public $timestamps = false;

    /** @return BelongsTo<Quote, QuoteIdMask> */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.quote'), 'entity_id', 'quote_id');
    }
}
