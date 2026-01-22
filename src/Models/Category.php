<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Models\Scopes\Category\IsActiveScope;
use Rapidez\Core\Models\Traits\HasAlternatesThroughRewrites;
use Rapidez\Core\Models\Traits\HasCustomAttributes;
use Rapidez\Core\Models\Traits\Searchable;
use TorMorten\Eventy\Facades\Eventy;

class Category extends Model
{
    use HasAlternatesThroughRewrites;
    use HasCustomAttributes;
    use Searchable;

    protected $table = 'catalog_category_entity';
    protected $primaryKey = 'entity_id';
    protected $entityTypeId = EavAttribute::ENTITY_TYPE_CATALOG_CATEGORY;

    protected $appends = ['url'];

    public const STATUS_ACTIVE = 1;

    protected static function booted(): void
    {
        static::addGlobalScope(IsActiveScope::class);
        static::withCustomAttributes();
    }

    protected function modifyRelation(HasMany $relation): HasMany
    {
        return $relation->leftJoin('catalog_eav_attribute', 'catalog_eav_attribute.attribute_id', '=', $relation->qualifyColumn('attribute_id'));
    }

    protected static function getEntityType(): string
    {
        return 'category';
    }

    public function subcategories()
    {
        return $this->hasMany(config('rapidez.models.category'), 'parent_id', 'entity_id');
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => '/' . ($this->url_path ?: ('catalog/category/view/id/' . $this->entity_id)));
    }

    public function products(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                config('rapidez.models.product'),
                'catalog_category_product',
                'category_id',
                'product_id',
                'entity_id',
                'entity_id',
            )
            ->withoutGlobalScopes();
    }

    protected function parentcategories(): Attribute
    {
        return Attribute::get(function () {
            $categoryIds = explode('/', $this->path);
            $categoryIds = array_slice($categoryIds, array_search(config('rapidez.root_category_id'), $categoryIds) + 1);

            return ! $categoryIds ? collect() : Category::whereIn($this->getQualifiedKeyName(), $categoryIds)
                ->orderByRaw('FIELD(' . $this->getQualifiedKeyName() . ',' . implode(',', $categoryIds) . ')')
                ->get();
        })->shouldCache();
    }

    /**
     * {@inheritdoc}
     */
    protected function makeAllSearchableUsing(Builder $query)
    {
        return $query
            ->withEventyGlobalScopes('index.' . static::getModelName() . '.scopes')
            ->whereAttributeNotNull('url_key')
            ->whereAttributeNot('url_key', 'default-category')
            ->has('products');
    }

    /**
     * {@inheritdoc}
     */
    public function toSearchableArray(): array
    {
        $data = $this->toArray();
        $data['parents'] = $this->parentcategories->pluck('name')->slice(0, -1)->toArray();

        return Eventy::filter('index.' . static::getModelName() . '.data', $data, $this);
    }

    public static function synonymFields(): array
    {
        return ['name'];
    }
}
