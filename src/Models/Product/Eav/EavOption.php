<?php

namespace Rapidez\Core\Models\Product\Eav;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;

class EavOption extends Model
{
    protected $table = 'eav_attribute_option';

    protected $guarded = [];

    public function optionValue(): BelongsTo
    {
        return $this->belongsTo(EavOptionValue::class, 'option_id', 'option_id');
    }
}
