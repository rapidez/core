<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Rapidez\Core\Models\Scopes\Product\ForCurrentWebsiteScope;
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

    protected $with = ['stock', 'superAttributes'];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(ForCurrentWebsiteScope::class);
        static::addGlobalScope('customAttributes', fn (Builder $builder) => $builder->withCustomAttributes());
        static::addGlobalScope('onlyEnabled', fn (Builder $builder) => $builder->whereValue('status', 1));
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

    public function parent(): HasOneThrough
    {
        return $this->hasOneThrough(
            ProductEntity::class,
            config('rapidez.models.product_link'),
            'product_id', 'entity_id',
            'entity_id', 'parent_id'
        );
    }

    public function children(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductEntity::class,
            config('rapidez.models.product_link'),
            'parent_id', 'entity_id',
            'entity_id', 'product_id'
        );
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(
            ProductStock::class,
            'entity_id',
            'product_id',
        );
    }

    public function superAttributes(): HasMany
    {
        return $this->hasMany(
            SuperAttribute::class,
            'product_id',
        )->orderBy('position');
    }

    public function superAttributeValues(): Attribute
    {
        return Attribute::get(function () {
            return $this->superAttributes->pluck('attribute_code')
                ->mapWithKeys(fn ($attribute) => [
                    $attribute => $this->children->mapWithKeys(function ($child) use ($attribute) {
                        return [$child->entity_id => [
                            'label' => $child->{$attribute},
                            'value' => $child->customAttributes[$attribute]->value_id,
                        ]];
                    }),
                ]);
        });
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
