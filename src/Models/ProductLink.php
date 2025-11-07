<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Rapidez\Core\Models\Traits\HasCustomAttributes;

class ProductLink extends Model
{
    use HasCustomAttributes;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'catalog_product_link';

    protected $attributeTypes = ['int', 'decimal', 'varchar'];
    protected $attributeTablePrefix = 'catalog_product_link_attribute';
    protected $attributeCode = 'product_link_attribute_code';

    protected $primaryKey = 'link_id';

    protected static function boot()
    {
        parent::boot();

        static::withCustomAttributes();
        static::addGlobalScope('withLinkType', fn (Builder $builder) => $builder->join('catalog_product_link_type', 'catalog_product_link_type.link_type_id', '=', 'catalog_product_link.link_type_id'));
    }

    protected function modifyRelation(HasMany $relation): HasMany
    {
        $relation->withoutGlobalScopes(['store', 'attribute']);
        $relation->withGlobalScope('productLinkAttribute', function (Builder $builder) {
            $builder->leftJoin('catalog_product_link_attribute', $builder->qualifyColumn('product_link_attribute_id'), '=', 'catalog_product_link_attribute.product_link_attribute_id');
        });

        return $relation;
    }

    public function linkedProduct(): HasOne
    {
        return $this->hasOne(config('rapidez.models.product'), 'entity_id', 'linked_product_id');
    }

    public function linkedParent(): HasOne
    {
        return $this->hasOne(config('rapidez.models.product'), 'entity_id', 'product_id');
    }
}
