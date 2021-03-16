<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use TorMorten\Eventy\Facades\Eventy;

class Category extends Model
{
    protected $primaryKey = 'entity_id';

    protected $appends = ['url'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope('defaults', function (Builder $builder) {
            $builder
                ->select(parent::getQueryAttributes())
                ->groupBy($builder->getQuery()->from . '.entity_id');
        });

        $scopes = Eventy::filter('category.scopes') ?: [];
        foreach ($scopes as $scope) {
            static::addGlobalScope(new $scope());
        }
    }

    public function getTable()
    {
        return 'catalog_category_flat_store_' . config('rapidez.store');
    }

    public function getUrlAttribute(): string
    {
        return '/' . $this->url_path . Config::getCachedByPath('catalog/seo/category_url_suffix', '.html');
    }

    public function getQueryAttributes() : array
    {
        return [
                $this->getTable() . '.entity_id',
                $this->getTable() . '.meta_title',
                $this->getTable() . '.name',
                $this->getTable() . '.is_anchor',
                $this->getTable() . '.description',
                $this->getTable() . '.is_active',
                $this->getTable() . '.include_in_menu',
                $this->getTable() . '.path',
                $this->getTable() . '.parent_id',
                $this->getTable() . '.children',
                $this->getTable() . '.position',
                $this->getTable() . '.url_path'
            ];
    }
}
