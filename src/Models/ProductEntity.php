<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Traits\HasEavAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

// TODO: Rename this to Product once finished, and extend the Rapidez base model.
class ProductEntity extends Model
{
    use HasEavAttributes;

    public const VISIBILITY_NOT_VISIBLE = 1;
    public const VISIBILITY_IN_CATALOG = 2;
    public const VISIBILITY_IN_SEARCH = 3;
    public const VISIBILITY_BOTH = 4;

    protected $primaryKey = 'entity_id';

    protected $table = 'catalog_product_entity';

    protected $casts = [
        self::UPDATED_AT => 'datetime',
        self::CREATED_AT => 'datetime',
    ];

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('default', function (Builder $builder) {
            $builder->withCustomFields();
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

        return $this->eavValues[$key]?->value ?? null;
    }
}
