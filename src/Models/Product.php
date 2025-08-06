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
use Rapidez\Core\Models\Traits\HasAlternatesThroughRewrites;
use Rapidez\Core\Models\Traits\HasCustomAttributes;

class Product extends Model
{
    use HasAlternatesThroughRewrites;
    use HasCustomAttributes;

    public const VISIBILITY_NOT_VISIBLE = 1;
    public const VISIBILITY_IN_CATALOG = 2;
    public const VISIBILITY_IN_SEARCH = 3;
    public const VISIBILITY_BOTH = 4;

    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 2;

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
        static::addGlobalScope('onlyEnabled', fn (Builder $builder) => $builder->whereAttribute('status', static::STATUS_ENABLED));
    }

    protected static function getEntityType(): string
    {
        return 'product';
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
            config('rapidez.models.product'),
            config('rapidez.models.product_link'),
            'product_id', 'entity_id',
            'entity_id', 'parent_id'
        );
    }

    public function children(): HasManyThrough
    {
        return $this->hasManyThrough(
            config('rapidez.models.product'),
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

    public function options(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.product_option'),
            'product_id',
        );
    }

    public function categoryProducts(): HasMany
    {
        return $this
            ->hasMany(
                config('rapidez.models.category_product'),
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
            return $this->superAttributes
                ->sortBy('position')
                ->mapWithKeys(fn ($attribute) => [
                    $attribute->attribute_code => $this->children->mapWithKeys(function ($child) use ($attribute) {
                        return [$child->entity_id => [
                            'label'      => $child->{$attribute->attribute_code},
                            'value'      => $child->customAttributes[$attribute->attribute_code]->value_id,
                            'sort_order' => $child->customAttributes[$attribute->attribute_code]->sort_order,
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

        $value = parent::getAttribute($key);
        if ($value !== null) {
            return $value;
        }

        return $this->getCustomAttribute($key)?->value;
    }
}
