<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Scopes\Product\ForCurrentWebsiteScope;
use Rapidez\Core\Models\Traits\HasAlternatesThroughRewrites;
use Rapidez\Core\Models\Traits\HasCustomAttributes;
use Rapidez\Core\Models\Traits\Product\HasSuperAttributes;

class Product extends Model
{
    use HasAlternatesThroughRewrites;
    use HasCustomAttributes;
    use HasSuperAttributes;

    public const VISIBILITY_NOT_VISIBLE = 1;
    public const VISIBILITY_IN_CATALOG = 2;
    public const VISIBILITY_IN_SEARCH = 3;
    public const VISIBILITY_BOTH = 4;

    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 2;

    protected $table = 'catalog_product_entity';
    protected $primaryKey = 'entity_id';

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

    protected function images(): Attribute
    {
        return Attribute::get(
            fn (): array => $this->gallery->sortBy('productImageValue.position')->pluck('value')->toArray()
        )->shouldCache();
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
            config('rapidez.models.product_stock'),
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

    private function getImageFrom(?string $image): ?string
    {
        return $image !== 'no_selection' ? $image : null;
    }

    protected function image(): Attribute
    {
        return Attribute::get($this->getImageFrom(...));
    }

    protected function smallImage(): Attribute
    {
        return Attribute::get($this->getImageFrom(...));
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::get($this->getImageFrom(...));
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/' . ($this->url_key ? $this->url_key . Rapidez::config('catalog/seo/product_url_suffix') : 'catalog/product/view/id/' . $this->entity_id)
        );
    }

    protected function price(): Attribute
    {
        return Attribute::get(fn (?float $price): ?float => $this->prices?->min() ?? $price)->shouldCache();
    }

    protected function prices(): Attribute
    {
         return Attribute::get(function (): ?Collection {
            if (! in_array($this->type_id, ['configurable', 'grouped'])) {
                return null;
            }

            return collect($this->type_id == 'configurable' ? $this->children : $this->grouped)->pluck('price');
        })->shouldCache();
    }

    protected function specialPrice(): Attribute
    {
        return Attribute::get(function (?float $specialPrice): ?float {
            if (! in_array($this->type_id, ['configurable', 'grouped'])) {
                if (!now()->isBetween(
                    $this->special_from_date ?? now()->subHour(),
                    $this->special_to_date ?? now()->addHour(),
                )) {
                    return null;
                }

                return $specialPrice < $this->price ? $specialPrice : null;
            }

            return collect($this->type_id == 'configurable' ? $this->children : $this->grouped)
                ->filter(fn ($child) => $child->specialPrice !== null)
                ->min->special_price;
        });
    }
}
