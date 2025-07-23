<?php

namespace Rapidez\Core\Models\Product\Eav;

use Rapidez\Core\Models\Product\EavAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\Model;

class EavVarchar extends Model
{
    protected $table = 'catalog_product_entity_varchar';

    protected $guarded = [];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(EavAttribute::class, 'attribute_id', 'attribute_id');
    }
}
