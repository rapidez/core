<?php

namespace Rapidez\Core\Models\Product;

use Rapidez\Core\Models\Product\Eav\EavDatetime;
use Rapidez\Core\Models\Product\Eav\EavDecimal;
use Rapidez\Core\Models\Product\Eav\EavInt;
use Rapidez\Core\Models\Product\Eav\EavText;
use Rapidez\Core\Models\Product\Eav\EavVarchar;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

class EavAttribute extends Model
{
    protected $table = 'eav_attribute';

    use HasFactory;

    protected $guarded = [];

    public function values(): Attribute
    {
        return Attribute::make(
            fn () => match ($this->relatedModel) {
                EavVarchar::class => $this->varcharValues,
                EavText::class => $this->textValues,
                EavInt::class => $this->intValues,
                EavDecimal::class => $this->decimalValues,
                EavDatetime::class => $this->datetimeValues,
                default => null,
            }
        );
    }

    public function relatedModel(): Attribute
    {
        return Attribute::make(
            fn () => Relation::getMorphedModel($this->type) ?? $this->type,
        );
    }

    public function varcharValues(): HasMany
    {
        return $this->hasMany(EavVarchar::class, 'attribute_id', 'attribute_id');
    }

    public function textValues(): HasMany
    {
        return $this->hasMany(EavText::class, 'attribute_id', 'attribute_id');
    }

    public function intValues(): HasMany
    {
        return $this->hasMany(EavInt::class, 'attribute_id', 'attribute_id');
    }

    public function decimalValues(): HasMany
    {
        return $this->hasMany(EavDecimal::class, 'attribute_id', 'attribute_id');
    }

    public function datetimeValues(): HasMany
    {
        return $this->hasMany(EavDatetime::class, 'attribute_id', 'attribute_id');
    }
}
