<?php

namespace Rapidez\Core\Models\Product\Eav;

use Rapidez\Core\Models\Product\EavAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;

class EavInt extends Model
{
    protected $table = 'catalog_product_entity_int';

    protected $guarded = [];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(EavAttribute::class, 'attribute_id', 'attribute_id');
    }

    public function optionValue(): BelongsTo
    {
        return $this->belongsTo(EavOptionValue::class, 'value', 'option_id');
    }

    public function value(): Attribute
    {
        return Attribute::make(
            function ($value) {
                if ($this->relationLoaded('optionValue') && $this->attribute->frontend_input === 'select' && $this->attribute->source_model === null) {
                    return $this->optionValue?->value ?? $value;
                }
                return (int) $value;
            }
        );
    }
}
