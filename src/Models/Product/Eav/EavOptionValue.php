<?php

namespace Rapidez\Core\Models\Product\Eav;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;

class EavOptionValue extends Model
{
    protected $table = 'eav_attribute_option_value';

    protected $guarded = [];

    public function option(): BelongsTo
    {
        return $this->belongsTo(EavOption::class, 'option_id', 'option_id');
    }
}
