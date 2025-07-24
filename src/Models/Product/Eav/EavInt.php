<?php

namespace Rapidez\Core\Models\Product\Eav;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Product\EavAttribute;

class EavInt extends AbstractEav
{
    protected $table = 'catalog_product_entity_int';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('default', function ($query) {
            $query->with('optionValue');
        });
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
