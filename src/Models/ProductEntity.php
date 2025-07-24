<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rapidez\Core\Models\Traits\HasCustomAttributes;

class ProductEntity extends Model
{
    use HasCustomAttributes;

    protected $table = 'catalog_product_entity';
    protected $primaryKey = 'entity_id';

    protected $casts = [
        self::UPDATED_AT => 'datetime',
        self::CREATED_AT => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('customAttributes', function (Builder $builder) {
            $builder->withCustomAttributes();
        });
    }

    public function gallery(): BelongsToMany
    {
        return $this->belongsToMany(
            config('rapidez.models.product_image'),
            'catalog_product_entity_media_gallery_value_to_entity',
            'entity_id',
            'value_id',
        );
    }

    public function getAttribute($key)
    {
        if (! $key) {
            return;
        }

        if ($value = parent::getAttribute($key)) {
            return $value;
        }

        return $this->getCustomAttribute($key)?->value;
    }
}
