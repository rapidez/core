<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Scopes\ForCurrentWebsiteWithoutLimitScope;

class WeeeTax extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'weee_tax';
    protected $primaryKey = 'value_id';

    protected $casts = [
        'value' => 'float',
    ];

    protected static function booting()
    {
        static::addGlobalScope('website', new ForCurrentWebsiteWithoutLimitScope(['entity_id', 'attribute_id']));
        static::addGlobalScope('withAttributeData', fn (Builder $query) => $query->leftJoin('eav_attribute', 'eav_attribute.attribute_id', 'weee_tax.attribute_id')
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            config('rapidez.models.product'),
            'entity_id',
            'entity_id',
        );
    }
}
