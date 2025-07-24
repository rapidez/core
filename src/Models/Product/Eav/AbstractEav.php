<?php

namespace Rapidez\Core\Models\Product\Eav;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Product\EavAttribute;
use Rapidez\Core\Models\Scopes\ForCurrentStoreWithoutLimitScope;

abstract class AbstractEav extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ForCurrentStoreWithoutLimitScope(['entity_id', 'attribute_id']));
        static::addGlobalScope('default', function ($query) {
            $query->with('attribute');
        });
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(EavAttribute::class, 'attribute_id', 'attribute_id');
    }
}
